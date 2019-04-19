<?php

namespace application\v1\controller;

use Fastapi\Http\Controller;
use application\v1\model\Api;

class Index extends Controller {
    
//    public function beforeAction() {
//        if ($this->request->server('remote_addr') == '192.168.112.58') {
//            return 'IP 192.168.112.58 is forbidden.';
//        }
//        return true;
//    }

    public function indexAction() {
        $apiModel = new Api();
        $apiConfig = $apiModel->getAllData();
        $this->response->sendJson($apiConfig);
    }
}
