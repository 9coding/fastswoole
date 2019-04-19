<?php

namespace Fastapi\Service;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Fastapi\Log\Log;

class LogProvider implements ServiceProviderInterface {

    public function register(Container $pimple) {
        $pimple['log'] = function ($c) {
            return new Log('fastapi');
        };
    }

}
