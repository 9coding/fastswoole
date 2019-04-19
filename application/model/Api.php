<?php

namespace application\v1\model;

use Fastapi\Model\Mysql;

class Api extends Mysql{

    public function getAllData($status = 1) {
        $stmt = $this->model->prepare('select api_id,api_key,api_address from apis where api_status = ?');
        return $stmt->execute(array($status));
    }
}
