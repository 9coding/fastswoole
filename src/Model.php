<?php

namespace FastSwoole;

use FastSwoole\Core;

class Model {
    
    public $db;
    
    public $table;
    
    public $primaryKey;

    public function __construct() {
        $this->db = Core::$app['mysql']->fetch();
    }
    
    public function __destruct() {
        Core::$app['mysql']->recycle($this->db);
    }
    
    public function query($sql, $timeout = 5) {
        $this->db->query($sql, $timeout);
        return $this->db->recv($timeout);
    }
    
    public function execute($sql, $bind = '') {
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($bind);
    }
}
