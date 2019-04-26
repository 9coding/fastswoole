<?php

namespace FastSwoole\Model;

use FastSwoole\Core;

class Mysql {
    
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
        if ($stmt) {
            return $this->db->execute($bind);
        } else {
            echo $this->db->error."\n";
            return false;
        }
    }
}
