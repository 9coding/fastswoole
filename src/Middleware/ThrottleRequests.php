<?php

namespace FastSwoole\Middleware;

use FastSwoole\Exception\ServerException;
use Stiphle\Throttle\LeakyBucket;
use League\Pipeline\StageInterface;

class ThrottleRequests extends StageInterface {
    
    private $throttle;
    
    public $params;

    public function __construct($params) {
        $this->params = $params;
        $this->throttle = new LeakyBucket();
    }
    
    public function __invoke($request) {
        $identifier = sha1($request->server['request_uri'].'|'.$request->server['remote_addr']);
        $limitCondition = explode(',', $this->params);
        $result = $this->throttle->throttle($identifier, $limitCondition[0], $limitCondition[1]*60000);
        if ($result !== 0) {
            throw new ServerException(403);
        }
        return 1;
    }

}
