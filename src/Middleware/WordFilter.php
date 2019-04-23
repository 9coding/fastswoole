<?php

namespace FastSwoole\Middleware;

use League\Pipeline\StageInterface;

class WordFilter extends StageInterface {

    public $params;
    
    public function __construct($params) {
        $this->params = $params;
    }
    
    public function __invoke($message) {
        $newMsg = str_replace($this->params, '', $message);
        return $newMsg;
    }

}
