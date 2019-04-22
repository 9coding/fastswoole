<?php

namespace application\model;

use FastSwoole\Model;

class User extends Model{

    public function getAllData() {
        $result = $this->model->query('select * from chatroom_user');
        $list = $result->recv();
        return $list;
    }
}
