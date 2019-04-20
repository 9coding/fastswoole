<?php

namespace FastSwoole\Service;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Noodlehaus\Config;

class ConfigProvider implements ServiceProviderInterface {

    public function register(Container $pimple) {
        $pimple['config'] = function ($c) {
            $allConfig = array();
            foreach (glob(CONFIG_DIR.'/*.php') as $configFile) {
                $filename = explode('.', $configFile);
                $allConfig[$filename[0]] = new Config(CONFIG_DIR.'/'.$configFile);
            }
            return $allConfig;
        };
    }

}
