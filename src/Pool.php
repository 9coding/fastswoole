<?php

namespace Fastapi\Pool;

use Fastapi\Core;
use Swoole\Coroutine\Channel;
use Swoole\Table;

class Pool {
    
    static protected $pool = [];
    
    static protected $connected = 0;
    
    static protected $config = [];
    
    static protected $popBox = [];
    
    static protected $runMode = '';
    
    static protected $baseMode = 'base';

    public static function init($config = []) {
        self::$runMode = $config['mode'] ?? 'base';
        unset($config['mode']);
        foreach ($config as $ckey => $cvalue) {
            if (!is_array($cvalue)) {
                self::$config[self::$baseMode][$ckey] = $cvalue;
            } else {
                foreach ($cvalue as $key => $value) {
                    self::$config[$ckey][$key] = $value;
                }
            }
        }
        foreach (self::$config as $key => $value) {
            if ($key != self::$baseMode) {
                foreach (self::$config[self::$baseMode] as $basekey => $basevalue) {
                    if (!isset(self::$config[$key][$basekey])) {
                        self::$config[$key][$basekey] = $basevalue;
                    }
                }
            }
        }
        self::$pool = new Channel(self::$config[self::$runMode]['maxConnnectNum']);
    }
}
