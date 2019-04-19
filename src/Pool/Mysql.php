<?php

namespace Fastapi\Pool;

use Swoole\Coroutine\MySQL as CoMysql;
use Swoole\Coroutine\Channel;

class Mysql extends Pool {
    
    public static function fetch() : CoMysql {
        if (!self::$pool instanceof Channel) {
            throw new \Exception('mysql pool do not init.');
        }
        if (self::$connected < self::$config[self::$runMode]['maxConnnectNum']) {
            $mysqlConnect = new CoMysql();
            if ($mysqlConnect->connect(self::$config[self::$runMode]) === false) {
                throw new \Exception('Can not connect to mysql : '.$mysqlConnect->connect_error);
            }
            self::$pool->push($mysqlConnect);
            self::$connected++;
        } else {
            $mysqlConnect = self::$pool->pop();
            if (!$mysqlConnect->connected) {
                self::$connected--;
                throw new \Exception('pop connect is error.');
            }
            $unique = spl_object_hash($mysqlConnect);
            self::$popBox[$unique] = self::$runMode;
        }
        return $mysqlConnect;
    }
    
    public static function recycle(CoMysql $mysql) {
        $unique = spl_object_hash($mysql);
        if (!$mysql->connected) {
            self::$connected--;
            unset(self::$popBox[$unique]);
            return false;
        }
        if (self::$connected > self::$config[self::$runMode]['maxConnnectNum']) {
            self::$connected--;
        }
        if (!isset(self::$popBox[$unique])) {
            return false;
        }
        if (self::$pool->length() < self::$config[self::$runMode]['maxConnnectNum']) {
            self::$pool->push($mysql);
        }
        unset(self::$popBox[$unique]);
    }
}
