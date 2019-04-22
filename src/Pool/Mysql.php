<?php

namespace FastSwoole\Pool;

use Swoole\Coroutine\MySQL as CoMysql;
use FastSwoole\Pool as DBPool;
use FastSwoole\Core;

class Mysql extends DBPool {
    
    public function createConnect() {
        $mysqlConfig = Core::$app['config']->get('db.mysql');
        $mysqlConnect = new CoMysql();
        $mysqlConnect->connect($mysqlConfig);
        $mysqlConnect->setDefer();
        return $mysqlConnect;
    }
}
