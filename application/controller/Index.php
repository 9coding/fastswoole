<?php

namespace application\controller;

use FastSwoole\Http\Controller;
use application\model\User;

class Index extends Controller {
    
//    public function beforeAction() {
//        if ($this->request->server('remote_addr') == '192.168.112.58') {
//            return 'IP 192.168.112.58 is forbidden.';
//        }
//        return true;
//    }

    public function indexAction(User $userModel) {
        $userList = $userModel->getAllData();
        $this->response->sendJson($userList);
    }
}
