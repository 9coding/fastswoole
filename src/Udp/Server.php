<?php

namespace FastSwoole\Udp;

use FastSwoole\Core;
use FastSwoole\Server as FastSwooleServer;
use League\Pipeline\Pipeline;
use Swoole\Server;
use FastSwoole\Functions\ClassMethod;

class Server extends FastSwooleServer {
    
    use ClassMethod;

    public function run() {
        $this->server = new Server($this->config['monitor_ip'], $this->config['monitor_port'], SWOOLE_PROCESS, SWOOLE_SOCK_UDP);
        $this->setCallback();
        $this->server->on('Packet', [$this, 'onPacket']);
        $this->server->start();
    }
    
    public function onPacket(Server $server, $data, $client_info) {
        $pipeline = new Pipeline();
        $registeredMiddlewares = Core::$app['middleware']->fetchMiddleware();
        foreach ($registeredMiddlewares as $middleware) {
            $pipeline = $pipeline->pipe($middleware);
        }
        $data = $pipeline->process($data);
    }
}
