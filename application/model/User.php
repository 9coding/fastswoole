<?php

namespace application\model;

use FastSwoole\Model\Mysql as MysqlModel;

class User extends MysqlModel{

    public function getAllData() {
        return $this->query('select * from chatroom_user');
    }
    
    public function getInfoByFd($fd) {
        return $this->execute('select * from chatroom_user where user_fd = ?', array($fd));
    }
    
    public function insertData($fd) {
        $loginname = rand(1000, 9999);
        return $this->execute('insert into chatroom_user set user_fd = ?, user_loginname="'.$loginname.'", user_loginpw="123", user_role=1', array($fd));
    }
}
