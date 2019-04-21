<?php

namespace FastSwoole\Service;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use FastSwoole\Logger;

class LogProvider implements ServiceProviderInterface {

    public function register(Container $pimple) {
        $pimple['log'] = function ($c) {
            $logger = new Logger('fastswoole');
            $logger->pushHandler(TEMP_DIR.'/'.date('Y-m-d').'.log');
            return $logger;
        };
    }

}
