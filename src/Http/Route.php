<?php

namespace FastSwoole\Http;

use FastRoute\RouteCollector;
use function FastRoute\cachedDispatcher;
use FastRoute\Dispatcher as RouteDispatcher;
use FastSwoole\Exception\ServerException;
use FastSwoole\Core;
use FastSwoole\Functions\ClassMethod;

class Route {
    
    use ClassMethod;

    private $dispatcher;
    
    public function __construct() {
        $routeConfig = [];
        $routeFile = APP_DIR.'/config/route.php';
        if (file_exists($routeFile)) {
            $routeConfig = include_once $routeFile;
        }
        $this->dispatcher = cachedDispatcher(function(RouteCollector $r) use ($routeConfig) {
            foreach ($routeConfig as $method => $route) {
                $method = strtoupper($method);
                foreach ($route as $uri => $callback) {
                    $r->addRoute($method, '/'.$uri, $callback);
                }
            }
        }, [
            'cacheFile' => TEMP_DIR . '/route.cache',
            'cacheDisabled' => true,
        ]);
    }
    
    private function match($uri, $method) {
        $uri = rawurldecode($uri);
        $routeInfo = $this->dispatcher->dispatch($method, $uri);
        switch ($routeInfo[0]) {
            case RouteDispatcher::NOT_FOUND:
                return ['handle'=>$uri, 'vars'=>[], 'dispatch'=>'not_found'];
            case RouteDispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                throw new ServerException(405, 'Allow method is '.implode(',', $allowedMethods));
            case RouteDispatcher::FOUND:
                return ['handle'=>$routeInfo[1], 'vars'=>$routeInfo[2], 'dispatch'=>'found'];
            default:
                throw new ServerException(500, 'Internet Server Error');
        }
    }
    
    public function dispatch($request, $response) {
        $pos = stripos($request->server['request_uri'], '?');
        if ($pos !== false) {
            $url = substr($request->server['request_uri'], 0, $pos);
        } else {
            $url = $request->server['request_uri'];
        }
        if ($url != '/'){
            $url = strtolower(trim($url, '/'));
        }
        $urlPart = explode('/', $url);
        $appConfig = Core::$app['config'];
        $defaultController = $appConfig->get('app.http.default_controller');
        $defaultAction = $appConfig->get('app.http.default_action');
        if ($url == '/') {
            $routeUrl = $defaultController.'/'.$defaultAction;
        } elseif (count($urlPart) == 1) {
            $routeUrl = $urlPart[0].'/'.$defaultAction;
        } elseif (count($urlPart) == 2) {
            $routeUrl = $urlPart[0].'/'.$urlPart[1];
        } else {
            $routeUrl = $url;
        }

        $request_method = isset($request->server['request_method']) ? strtoupper($request->server['request_method']) : 'GET';
        $request->server['request_method'] = $request_method;
        $routeResult = $this->match('/'.$routeUrl, $request_method);
        $currentRequest = new Request($request, $routeResult['vars']);
        $handle_part = explode('/', trim($routeResult['handle'], '/'));
        $currentRequest->controller = '\application\\controller\\'.ucfirst($handle_part[0]);
        $currentRequest->action = $handle_part[1].'Action';
        $currentController = $currentRequest->controller;
        if (class_exists($currentController)) {
            $currentAction = $currentRequest->action;
            $reflaction = new \ReflectionClass($currentController);
            if (!$reflaction->hasMethod($currentAction)) {
                throw new ServerException(404, $currentAction.' method is not exists.');
            }
            $currentResponse = new Response($response);
            $controller = new $currentController($currentRequest, $currentResponse);
            $beforeActionResult = true;
            if ($reflaction->hasMethod('beforeAction')) {
                $beforeActionResult = $controller->beforeAction();
            }
            if ($beforeActionResult === true) {
//                $reflactionMethod = new \ReflectionMethod($currentController, $currentAction);
//                $methodParams = array();
//                foreach ($reflactionMethod->getParameters() as $param) {
//                    $paramType = $param->getType();
//                    if (!$paramType->isBuiltin()) {
//                        $paramObject = strval($paramType);
//                        $methodParams[] = new $paramObject;
//                    } else {
//                        $methodParams[] = $param->getDefaultValue();
//                    }
//                }
                $methodParams = $this->analyzeParameter($currentController, $currentAction);
                call_user_func_array(array($controller, $currentAction), $methodParams);
                foreach ($methodParams as $param) {
                    unset($param);
                }
            } else {
                throw new ServerException(403, $beforeActionResult);
            }
        } else {
            throw new ServerException(404, $currentController.' controller is not exists.');
        }
    }
}
