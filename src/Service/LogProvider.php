<?php

namespace FastSwoole\Service;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use FastSwoole\Log\Log;

class LogProvider implements ServiceProviderInterface {

    public function register(Container $pimple) {
        $pimple['log'] = function ($c) {
            return new Log('fastapi');
        };
    }

}
