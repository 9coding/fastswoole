<?php

namespace application\websocket;

use application\model\User;

class Open {

    public function execute($request, User $userModel) {
        $result = true;
        $userInfo = $userModel->getInfoByFd($request->fd);
        if (!$userInfo) {
            $result = $userModel->insertData($request->fd);
        } elseif ($userInfo['user_status'] == 1) {
            $result = false;
        }
        return $result;
    }
}
