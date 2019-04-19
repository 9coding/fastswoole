<?php
namespace application\v1\controller;

use Fastapi\Http\Controller;
use application\v1\model\Task as TaskModel;

class Task extends Controller {

    public function fetchAction() {
        $postdata = $this->request->post();
        $taskModel = new TaskModel();
        $result = $taskModel->fetch($postdata['mid']);
        $this->response->sendJson(['task_name'=>'tmall_detail']);
    }
}
