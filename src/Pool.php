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
        }
        $this->pool = new Channel($this->maxConnect+1);
    }
    
    public function fetch() {
        if ($this->pool->isEmpty()) {
            if ($this->connected < $this->maxConnect) {
                $mysqlConnect = $this->createConnect($this->config);
                $this->connected++;
            } else {
                $mysqlConnect = $this->pool->pop(3);
            }
        } else {
            $mysqlConnect = $this->pool->pop(3);
        }
        if (!$mysqlConnect || !$mysqlConnect->connected) {
            throw new ServerException(504, 'Gateway Time-out');
        }
        echo date('H;i;s').'fetch pop后连接池剩余长度'.$this->pool->length()."\n";
        return $mysqlConnect;
    }
    
    public function recycle($connect) {
        if (!$connect || !$connect->connected) {
            $this->connected--;
            return false;
        }
        if ($this->connected > $this->maxConnect) {
            $this->connected--;
        }
        if ($this->pool->length() < $this->maxConnect) {
            $this->pool->push($connect);
        }
        echo date('H;i;s').'recycle push后连接池剩余长度'.$this->pool->length()."\n";
    }
}
