<?php

namespace FastSwoole\Service;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use FastSwoole\Language;

class LanguageProvider implements ServiceProviderInterface {

    public function register(Container $pimple) {
        $pimple['lang'] = function ($c) {
            return new Language();
        };
    }

}
