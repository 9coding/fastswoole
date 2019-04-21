<?php

namespace FastSwoole\Middleware;

use FastSwoole\Core;

class MiddlewareManager {

    private function getMiddlewares() {
        $middlewares = Core::$app['config']->get('app.'.MODE.'.middleware');
        $confMiddlewares = [];
        if (is_array($middlewares)) {
            foreach ($middlewares as $middleware => $params) {
                $middleClass = '\FastSwoole\\Middleware\\'.$middleware;
                if ($params !== false && class_exists($middleClass)) {
                    $confMiddlewares[$middleware] = $params;
                }
            }
        }
        return $confMiddlewares;
    }

    public function registeMiddleware() {
        $middlewares = $this->getMiddlewares();
        foreach ($middlewares as $middleware => $params) {
            $middleClass = '\FastSwoole\\Middleware\\'.$middleware;
            Core::$app[$middleware] = function ($c) use ($middleClass, $params) {
                return new $middleClass($params);
            };
        }
    }
    
    public function fetchMiddleware() {
        $middlewares = $this->getMiddlewares();
        $confMiddlewares = [];
        foreach ($middlewares as $middleware => $params) {
            $confMiddlewares[] = Core::$app[$middleware];
        }
        return $confMiddlewares;
    }
}
