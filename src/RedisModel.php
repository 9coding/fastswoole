<?php

namespace FastSwoole;

use FastSwoole\Core;

class RedisModel {
    
    public $db;

    public function __construct() {
        $this->db = Core::$app['redis']->fetch();
    }
    
    public function __destruct() {
        Core::$app['redis']->recycle($this->db);
    }
}
