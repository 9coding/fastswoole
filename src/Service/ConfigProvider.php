<?php

namespace Fastapi\Service;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Noodlehaus\Config;

class ConfigProvider implements ServiceProviderInterface {

    public function register(Container $pimple) {
        $pimple['config'] = function ($c) {
            return new Config(CONFIG_DIR);
        };
    }

}
