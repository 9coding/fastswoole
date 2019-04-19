<?php

namespace application\v1\model;

use Fastapi\Model\Mysql;

class Task extends Mysql{

    public function fetch($mid = '') {
        $clusterId = '';
        if ($mid) {
            $stmt = $this->model->prepare('select slave_cluster from slaves where slave_code = ?');
            $slave = $stmt->execute(array($mid));
            if (isset($slave[0]) && $slave[0]) {
                $clusterId = $slave[0]['slave_cluster'];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
