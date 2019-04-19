<?php
namespace application\v1\controller;

use Fastapi\Http\Controller;
use application\v1\model\Slave as SlaveModel;

class Slave extends Controller {

    public function registeAction() {
        $postdata = $this->request->post();
        $slaveModel = new SlaveModel();
        $result = $slaveModel->registe($postdata['mid'], $postdata['info']);
        $this->response->sendJson($result);
    }
}
