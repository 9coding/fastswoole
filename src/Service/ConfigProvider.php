<?php

namespace FastSwoole\Service;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use FastSwoole\Config;

class ConfigProvider implements ServiceProviderInterface {

    public function register(Container $pimple) {
        $pimple['config'] = function ($c) {
            $config = new Config();
            $config->load();
            return $config;
        };
    }

}
