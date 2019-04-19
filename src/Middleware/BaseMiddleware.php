<?php

namespace Fastapi\Middleware;

use League\Pipeline\StageInterface;
use Fastapi\Core;

class BaseMiddleware implements StageInterface {
    
    protected $params;

    public function __construct($params) {
        $this->params = $params;
    }

    public function __invoke($request) {
        return $this->handle($request);
    }
    
    private static function getMiddlewares() {
        $middlewares = Core::$container['config']['middleware'];
        $confMiddlewares = [];
        if (is_array($middlewares)) {
            foreach ($middlewares as $middleware => $params) {
                $middleClass = '\Fastapi\\Middleware\\'.$middleware;
                if ($params !== false && class_exists($middleClass)) {
                    $confMiddlewares[$middleware] = $params;
                }
            }
        }
        return $confMiddlewares;
    }

    public static function registeMiddleware() {
        $middlewares = self::getMiddlewares();
        foreach ($middlewares as $middleware => $params) {
            $middleClass = '\Fastapi\\Middleware\\'.$middleware;
            Core::$container['middleware_param'] = $params;
            Core::$container[$middleware] = function ($c) use ($middleClass) {
                return new $middleClass($c['middleware_param']);
            };
        }
    }
    
    public static function fetchMiddleware() {
        $middlewares = self::getMiddlewares();
        $confMiddlewares = [];
        foreach ($middlewares as $middleware => $params) {
            $confMiddlewares[] = Core::$container[$middleware];
        }
        return $confMiddlewares;
    }
}
