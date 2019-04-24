<?php

namespace FastSwoole;

use FastSwoole\Core;
use Swoole\Coroutine\Channel;
use FastSwoole\Exception\ServerException;

class Pool {
    
    public $pool;
    
    public $connected = 0;
    
    public $maxConnect = 10;
    
    public $config;

    public function __construct() {
        $className = explode('\\', strtolower(get_class($this)));
        $classType = array_pop($className);
        $this->config = Core::$app['config']->get('db.'.$classType);
        if (isset($this->config['max_connnect']) && $this->config['max_connnect'] > 0) {
            $this->maxConnect = $this->config['max_connnect'];
            $this->pool = new Channel($this->maxConnect+1);
            swoole_timer_tick(3000, function () {
                echo date('H:i:s').' - 当前连接池拥有的连接数量：'.$this->pool->length()."\n";
            });
        } else {
            $this->pool = false;
        }
    }
    
    public function fetch() {
        if ($this->pool === false) {
            $dbConnect = $this->createConnect($this->config);
        } else {
            if ($this->pool->isEmpty()) {
                if ($this->connected < $this->maxConnect) {
                    $dbConnect = $this->createConnect($this->config);
                    $this->connected++;
                } else {
                    $dbConnect = $this->pool->pop(3);
                }
            } else {
                $dbConnect = $this->pool->pop(3);
            }
        }
        if (!$dbConnect || !$dbConnect->connected) {
            $this->connected--;
            throw new ServerException(504, 'Gateway Time-out');
        }
        return $dbConnect;
    }
    
    public function recycle($connect) {
        if (!$connect || !$connect->connected) {
            $this->connected--;
            return false;
        }
        if ($this->pool === false) {
            unset($connect);
            return true;
        }
        if ($this->connected > $this->maxConnect) {
            $this->connected--;
        }
        if ($this->pool->length() < $this->maxConnect) {
            $this->pool->push($connect);
        }
    }
}
