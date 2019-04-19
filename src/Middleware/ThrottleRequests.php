<?php

namespace Fastapi\Middleware;

use Fastapi\Exception\ServerException;
use Stiphle\Throttle\LeakyBucket;

class ThrottleRequests extends BaseMiddleware {
    
    private $throttle;

    public function __construct($params) {
        $this->params = $params;
        $this->throttle = new LeakyBucket();
    }

    public function handle($request) {
        $identifier = sha1($request->server['request_uri'].'|'.$request->server['remote_addr']);
        $limitCondition = explode(',', $this->params);
        $result = $this->throttle->throttle($identifier, $limitCondition[0], $limitCondition[1]*60000);
        if ($result !== 0) {
            throw new ServerException(403);
        }
    }

}
