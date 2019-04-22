<?php

namespace application\model;

use FastSwoole\Model;

class User extends Model{

    public function getAllData() {
        return $this->model->query('select * from chatroom_user');
    }
}
