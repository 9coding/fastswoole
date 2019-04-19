<?php

namespace application\v1\model;

use Fastapi\Model\Mysql;

class Slave extends Mysql{

    public function registe($mid = '', $info = []) {
        $slave = '';
        if ($mid) {
            $stmt = $this->model->prepare('select slave_name from slaves where slave_code = ?');
            $slave = $stmt->execute(array($mid));
            if ($slave) {
                $slave = $slave[0];
            }
        } else {
            return false;
        }
        if (!$slave) {
            $this->model->query('insert into slaves (slave_name,slave_code,slave_register) values ("'.$info['host'].'","'.$mid.'","'.date('Y-m-d H:i:s').'")');
            return $this->model->insert_id;
        }
        return $slave;
    }
}
