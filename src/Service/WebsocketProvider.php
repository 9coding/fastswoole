<?php

namespace FastSwoole\Service;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use FastSwoole\Websocket\Server;

class WebsocketProvider implements ServiceProviderInterface {

    public function register(Container $pimple) {
        $pimple['websocket'] = function ($c) {
            return new Server();
        };
    }

}
