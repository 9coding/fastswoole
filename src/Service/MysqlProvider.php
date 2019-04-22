<?php

namespace FastSwoole\Service;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use FastSwoole\Pool\Mysql as MysqlPool;

class MysqlProvider implements ServiceProviderInterface {

    public function register(Container $pimple) {
        $pimple['mysql'] = function ($c) {
            $mysqlpool = new MysqlPool();
            $mysqlpool->setDefer();
            return $mysqlpool;
        };
    }

}
