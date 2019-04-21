<?php

namespace FastSwoole\Service;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use FastSwoole\Http\Server;

class HttpServerProvider implements ServiceProviderInterface {

    public function register(Container $pimple) {
        $pimple['http'] = function ($c) {
            return new Server();
        };
    }

}
