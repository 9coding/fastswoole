<?php

namespace FastSwoole\Tcp;

use Swoole\Server;

class Controller {

    protected $server;
    
    protected $fd;
    
    protected $reactorId;
    
    protected $data = '';

    public function __construct(Server $server, $param) {
        $this->server = $server;
        $this->fd = $param[0];
        $this->reactorId = $param[1];
        if (isset($param[2])) {
            $this->data = $param[2];
        }
    }
}
