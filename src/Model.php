<?php

namespace FastSwoole;

use FastSwoole\Core;

class Model {
    
    public $model;
    
    public $table;
    
    public $primaryKey;

    public function __construct() {
        $this->model = Core::$app['mysql']->fetch();
    }
    
    public function __destruct() {
        Core::$app['mysql']->recycle($this->model);
    }
    
    public function query($sql, $timeout = 5) {
        $this->model->query($sql, $timeout);
        return $this->model->recv();
    }
}
