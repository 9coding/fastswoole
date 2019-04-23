<?php

namespace FastSwoole\Pool;

use Swoole\Coroutine\Redis as CoRedis;
use FastSwoole\Pool as DBPool;

class Redis extends DBPool {

    public function createConnect($redisConfig) {
        $redisConnect = new CoRedis();
        $redisConnect->connect($redisConfig);
        $redisConnect->setDefer();
        return $redisConnect;
    }
}
