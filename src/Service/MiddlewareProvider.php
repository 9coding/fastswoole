<?php

namespace FastSwoole\Service;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use FastSwoole\MiddlewareManager;

class MiddlewareProvider implements ServiceProviderInterface {

    public function register(Container $pimple) {
        $pimple['middleware'] = function ($c) {
            $middlewareManager = new MiddlewareManager();
            $middlewareManager->registeMiddleware();
            return $middlewareManager; 
        };
    }

}
