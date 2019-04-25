<?php

namespace FastSwoole\Websocket;

use Swoole\WebSocket\Server as WebSocketServer;

class Controller {

    protected $server;
    
    protected $param;

    public function __construct(WebSocketServer $server, $param) {
        $this->server = $server;
        $this->param = $param;
    }
}
