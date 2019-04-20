<?php

namespace FastSwoole\Service;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use FastSwoole\Exception\ServerError;
use FastSwoole\Exception\ServerException;

class ErrorProvider implements ServiceProviderInterface {

    public function register(Container $pimple) {
        $pimple['error'] = function ($c) {
            $error = new ServerError();
            register_shutdown_function(array($error, 'shutdown'));
            return new ServerException();
        };
    }

}
