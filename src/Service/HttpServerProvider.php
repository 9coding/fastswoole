<?php

namespace FastSwoole\Service;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use FastSwoole\Http\Server;

class HttpServerProvider implements ServiceProviderInterface {

    public function register(Container $pimple) {
        $pimple['server_config'] = $pimple['config']['http'];
        $pimple['httpserver'] = function ($c) {
            return new Server($c['server_config']);
        };
    }

}
