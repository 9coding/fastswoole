<?php

namespace FastSwoole\Websocket;

use FastSwoole\Core;
use FastSwoole\Server as FastSwooleServer;
use League\Pipeline\Pipeline;
use Swoole\WebSocket\Server as WebSocketServer;
use FastSwoole\Functions\ClassMethod;

class Server extends FastSwooleServer {
    
    use ClassMethod;
    
    public $server;

    public function __construct() {
        $this->config = Core::$app['config']->get('server.websocket');
    }

    public function run() {
        $this->server = new WebSocketServer($this->config['monitor_ip'], $this->config['monitor_port'], SWOOLE_PROCESS);
        $this->setCallback();
        $this->server->on('Open', [$this, 'onOpen']);
        $this->server->on('Message', [$this, 'onMessage']);
        $this->server->on('Close', [$this, 'onClose']);
        $this->server->start();
    }

    public function onOpen(WebSocketServer $server, $request) {
        try {
            $result = $this->dispatchMethod($server, '\\application\\websocket\\Open', 'execute', $request);
        } catch (\Exception $exc) {
            $server->push($request->fd, $exc->getMessage());
        }
    }
    
    public function onMessage(WebSocketServer $server, $frame) {
        $pipeline = new Pipeline();
        $registeredMiddlewares = Core::$app['middleware']->fetchMiddleware();
        foreach ($registeredMiddlewares as $middleware) {
            $pipeline = $pipeline->pipe($middleware);
        }
        $frame->data = $pipeline->process($frame->data);
        try {
            $result = $this->dispatchMethod($server, '\\application\\websocket\\Message', 'execute', $frame);
        } catch (\Exception $exc) {
            $server->push($frame->fd, $exc->getMessage());
        }
    }
    
    public function onClose(WebSocketServer $server, $closefd) {
        $result = $this->dispatchMethod($server, '\\application\\websocket\\Close', 'execute', $closefd);
    }
}
