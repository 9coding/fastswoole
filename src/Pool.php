<?php

namespace FastSwoole;

use FastSwoole\Core;
use Swoole\Coroutine\Channel;

class Pool {
    
    public $pool;
    
    public $connected = 0;
    
    public $popBox = [];
    
    public $maxConnect = 50;
    
    public $minConnect = 10;

    public function __construct() {
        $className = explode('\\', strtolower(get_class($this)));
        $classType = array_pop($className);
        $this->maxConnect = Core::$app['config']->get('db.'.$classType.'.max_connnect', 30);
        $this->pool = new Channel($this->maxConnect+1);
        while ($this->connected < $this->minConnect) {
            $mysqlConnect = $this->createConnect();
            $this->pool->push($mysqlConnect);
            $this->connected++;
        }
        echo 'init length '.$this->pool->length()."\n";
    }
    
    public function fetch() {
        if ($this->pool->isEmpty()) {
            if ($this->connected < $this->maxConnect) {
                $mysqlConnect = $this->createConnect();
                $this->connected++;
            } else {
                $mysqlConnect = $this->pool->pop(3);
            }
        } else {
            $mysqlConnect = $this->pool->pop(3);
        }
        if (!$mysqlConnect || !$mysqlConnect->connected) {
            $mysqlConnect = $this->createConnect();
        }
        echo date('H;i;s').'fetch pop后连接池剩余长度'.$this->pool->length()."\n";
        $unique = spl_object_hash($mysqlConnect);
        $this->popBox[$unique] = 1;
        echo date('H;i;s').'fetch popBox存储对象数量'.count($this->popBox)."\n";
        return $mysqlConnect;
    }
    
    public function recycle($connect) {
        $unique = spl_object_hash($connect);
        if (!$connect || !$connect->connected) {
            $this->connected--;
            unset($this->popBox[$unique]);
            return false;
        }
        if ($this->connected > $this->maxConnect) {
            $this->connected--;
        }
        if (!isset($this->popBox[$unique])) {
            return false;
        }
        if ($this->pool->length() < $this->maxConnect) {
            $this->pool->push($connect);
        }
        unset($this->popBox[$unique]);
        echo date('H;i;s').'recycle push后连接池剩余长度'.$this->pool->length()."\n";
        echo date('H;i;s').'recycle popBox存储对象数量'.count($this->popBox)."\n";
    }
    
    public function gc() {
        swoole_timer_tick(60000, function () {
            
        });
    }
}
