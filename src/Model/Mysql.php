<?php

namespace Fastapi\Model;

use Fastapi\Pool\Mysql as MysqlPool;

class Mysql {
    
    protected $model;

    public function __construct() {
        $this->model = MysqlPool::fetch();
    }
    
    public function __destruct() {
        MysqlPool::recycle($this->model);
    }
}
