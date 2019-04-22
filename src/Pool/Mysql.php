<?php

namespace FastSwoole\Pool;

use Swoole\Coroutine\MySQL as CoMysql;
use FastSwoole\Pool as DBPool;
use FastSwoole\Core;

class Mysql extends DBPool {
    
    public function createConnect() {
        $mysqlConfig = Core::$app['config']->get('db.mysql');
        $mysqlConnect = new CoMysql();
        if ($mysqlConnect->connect($mysqlConfig) === false) {
            throw new \Exception('Can not connect to mysql : '.$mysqlConnect->connect_error);
        }
        return $mysqlConnect;
    }
}
