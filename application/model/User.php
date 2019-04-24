<?php

namespace application\model;

use FastSwoole\Model\Mysql as MysqlModel;

class User extends MysqlModel{

    public function getAllData() {
        return $this->query('select * from chatroom_user');
    }
}
