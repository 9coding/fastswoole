<?php

namespace Fastapi\Service;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Fastapi\Http\Route;

class RouteProvider implements ServiceProviderInterface {

    public function register(Container $pimple) {
        $pimple['route'] = function ($c) {
            return new Route();
        };
    }

}
