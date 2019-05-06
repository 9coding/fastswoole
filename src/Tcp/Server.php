<?php

namespace FastSwoole\Tcp;

use FastSwoole\Server as FastSwooleServer;
use Swoole\Server;
use FastSwoole\Functions\ClassMethod;

class Server extends FastSwooleServer {
    
    use ClassMethod;

    public function run() {
        $this->server = new Server($this->config['monitor_ip'], $this->config['monitor_port'], SWOOLE_PROCESS);
        $this->setCallback();
        $this->server->on('Connect', [$this, 'onConnect']);
        $this->server->on('Receive', [$this, 'onReceive']);
        $this->server->on('Close', [$this, 'onClose']);
        $this->server->start();
    }

    public function onConnect(Server $server, $fd, $reactorId) {
        
    }
    
    public function onReceive(Server $server, $fd, $reactorId, $data) {
        $result = $this->dispatchMethod($server, '\\application\\tcp\\Receive', 'execute', $fd, $reactorId, $data);
    }
    
    public function onClose(Server $server, $fd, $reactorId) {
        
    }
}
