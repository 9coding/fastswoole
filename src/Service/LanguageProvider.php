<?php

namespace Fastapi\Service;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Fastapi\Language\Language;

class LanguageProvider implements ServiceProviderInterface {

    public function register(Container $pimple) {
        $pimple['default_language'] = $pimple['config']['default_language'];
        $pimple['lang'] = function ($c) {
            return new Language($c['default_language']);
        };
    }

}
