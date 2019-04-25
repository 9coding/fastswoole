<?php

namespace application\websocket;

use application\model\User;
use FastSwoole\Websocket\Controller;

class Open extends Controller {

    public function execute(User $userModel) {
        $userInfo = $userModel->getInfoByFd($this->param->fd);
        if (!$userInfo) {
            $result = $userModel->insertData($this->param->fd);
            if (!$result) {
                $this->server->disconnect($this->param->fd);
            }
        } elseif ($userInfo['user_status'] == 1) {
            $this->server->disconnect($this->param->fd);
        }
        $userList = $userModel->getAllData();
        foreach ($userList as $user) {
            if ($user['user_fd'] != $this->param->fd) {
                $this->server->push($user['user_fd'], json_encode(array('event'=>'open','type'=>'new_user','target'=>$this->param->fd)));
            }
        }
        $this->server->push($this->param->fd, json_encode(array('event'=>'open','type'=>'other_user','target'=>$userList,'self'=>$this->param->fd)));
    }
}
