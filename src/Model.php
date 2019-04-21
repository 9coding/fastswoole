<?php

namespace FastSwoole;

use FastSwoole\Core;

class Model {
    
    protected $model;

    public function __construct() {
        $this->model = Core::$app['mysql']->fetch();
    }
    
    public function __destruct() {
        Core::$app['mysql']->recycle($this->model);
    }
}
