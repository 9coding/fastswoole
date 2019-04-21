<?php

namespace FastSwoole\Pool;

use Swoole\Coroutine\Redis as CoRedis;
use FastSwoole\Pool as DBPool;

class Redis extends DBPool {

    public function createConnect() {
        $redisConfig = Core::$app['config']->get('db.redis');
        $redisConnect = new CoRedis();
        if ($redisConnect->connect($redisConfig) === false) {
            throw new \Exception('Can not connect to redis : '.$redisConfig->errMsg);
        }
        return $redisConnect;
    }
}
