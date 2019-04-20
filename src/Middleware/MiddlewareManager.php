<?php

namespace FastSwoole\Middleware;

use FastSwoole\Core;

class MiddlewareManager {

    private function getMiddlewares() {
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

    public function registeMiddleware() {
        $middlewares = $this->getMiddlewares();
        foreach ($middlewares as $middleware => $params) {
            $middleClass = '\Fastapi\\Middleware\\'.$middleware;
            Core::$container['middleware_param'] = $params;
            Core::$container[$middleware] = function ($c) use ($middleClass) {
                return new $middleClass($c['middleware_param']);
            };
        }
    }
    
    public function fetchMiddleware() {
        $middlewares = $this->getMiddlewares();
        $confMiddlewares = [];
        foreach ($middlewares as $middleware => $params) {
            $confMiddlewares[] = Core::$container[$middleware];
        }
        return $confMiddlewares;
    }
}
