<?php

namespace FastSwoole\Pool;

use Swoole\Coroutine\MySQL as CoMysql;
use FastSwoole\Pool as DBPool;

class Mysql extends DBPool {
    
    public function createConnect($mysqlConfig) {
        $mysqlConnect = new CoMysql();
        $mysqlConnect->connect($mysqlConfig);
        $mysqlConnect->setDefer();
        return $mysqlConnect;
    }
}
