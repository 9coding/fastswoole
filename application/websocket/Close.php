<?php

namespace application\websocket;

use FastSwoole\Websocket\Controller;

class Close extends Controller {

    public function execute(User $userModel) {
        $userList = $userModel->getAllData();
        foreach ($userList as $user) {
            if ($this->param != $user['user_fd']) {
                $this->server->push($user['user_fd'], json_encode(array('event'=>'close','type'=>'quit','target'=>$this->param)));
            }
        }
    }
}
