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
        return $this->execute('insert into chatroom_user set user_fd = ?', array($fd));
    }
}
