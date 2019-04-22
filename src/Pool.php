<?php

namespace FastSwoole;

use FastSwoole\Core;
use Swoole\Coroutine\Channel;

class Pool {
    
    public $pool;
    
    public $connected = 0;
    
    public $popBox = [];
    
    public $maxConnect;

    public function __construct() {
        $type = array_pop(explode('\\', strtolower(get_class($this))));
        echo 'type class name '.$type."\n";
        $this->maxConnect = Core::$app['config']->get('db.'.$type.'.max_connnect', 5);
        $this->pool = new Channel($this->maxConnect);
        while ($this->connected < $this->maxConnect) {
            $mysqlConnect = $this->createConnect();
            $this->pool->push($mysqlConnect);
            $this->connected++;
        }
        echo 'init length '.$this->pool->length()."\n";
    }
    
    public function fetch() {
        if ($this->pool->isEmpty()) {
            $mysqlConnect = $this->createConnect();
        } else {
            $mysqlConnect = $this->pool->pop();
            if (!$mysqlConnect->connected) {
                $mysqlConnect = $this->createConnect();
                $this->pool->push($mysqlConnect);
            }
        }
        echo 'fetch '.$this->pool->length()."\n";
        $unique = spl_object_hash($mysqlConnect);
        $this->popBox[$unique] = 1;
        echo 'fetch popBox length'.count($this->popBox)."\n";
        return $mysqlConnect;
    }
    
    public function recycle($connect) {
        $unique = spl_object_hash($connect);
        if (!$connect->connected) {
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
        echo 'recycle '.$this->pool->length()."\n";
        echo 'recycle popBox length'.count($this->popBox)."\n";
    }
}
