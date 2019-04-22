<?php

namespace application\model;

use FastSwoole\Model;

class User extends Model{

    public function getAllData() {
        $stmt = $this->model->prepare('select * from chatroom_user');
        return $stmt->execute();
    }
}
