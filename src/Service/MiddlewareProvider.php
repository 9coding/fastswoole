<?php

namespace FastSwoole\Service;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use FastSwoole\Middleware;

class MiddlewareProvider implements ServiceProviderInterface {

    public function register(Container $pimple) {
        $pimple['middleware'] = function ($c) {
            $middlewareManager = new Middleware();
            $middlewareManager->registeMiddleware();
            return $middlewareManager; 
        };
    }

}
