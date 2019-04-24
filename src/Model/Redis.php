<?php

namespace FastSwoole\Model;

use FastSwoole\Core;

class Redis {
    
    public $db;

    public function __construct() {
        $this->db = Core::$app['redis']->fetch();
    }
    
    public function __destruct() {
        Core::$app['redis']->recycle($this->db);
    }
}
