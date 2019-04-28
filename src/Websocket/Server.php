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
    
    private function dispatch($target, $data) {
        $className = '\\application\\websocket\\'.$target;
        if (class_exists($className)) {
            $reflaction = new \ReflectionClass($className);
            if ($reflaction->hasMethod('execute')) {
                $methodParams = $this->analyzeParameter($className, 'execute');
                $controller = new $className($this->server, $data);
                call_user_func_array(array($controller, 'execute'), $methodParams);
            }
        }
    }

    public function onOpen(WebSocketServer $server, $request) {
        try {
            $this->dispatch('Open', $request);
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
            $this->dispatch('Message', $frame);
        } catch (\Exception $exc) {
            $server->push($frame->fd, $exc->getMessage());
        }
    }
    
    public function onClose(WebSocketServer $server, $closefd) {
        $this->dispatch('Close', $closefd);
    }
}
