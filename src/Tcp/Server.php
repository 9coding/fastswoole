<?php

namespace FastSwoole\Tcp;

use FastSwoole\Core;
use FastSwoole\Server as FastSwooleServer;
use Swoole\Server;
use FastSwoole\Functions\ClassMethod;

class Server extends FastSwooleServer {
    
    use ClassMethod;
    
    public $server;

    public function __construct() {
        $this->config = Core::$app['config']->get('server.tcp');
    }

    public function run() {
        $this->server = new Server($this->config['monitor_ip'], $this->config['monitor_port'], SWOOLE_PROCESS);
        $this->setCallback();
        $this->server->on('Connect', [$this, 'onConnect']);
        $this->server->on('Receive', [$this, 'onReceive']);
        $this->server->on('Close', [$this, 'onClose']);
        $this->server->start();
    }
    
    private function dispatch($target, ...$data) {
        $className = '\application\\tcp\\'.$target;
        if (class_exists($className)) {
            $reflaction = new \ReflectionClass($className);
            if ($reflaction->hasMethod('execute')) {
                $methodParams = $this->analyzeParameter($className, 'execute');
                $controller = new $className($this->server, $data);
                call_user_func_array(array($controller, 'execute'), $methodParams);
            }
        }
    }

    public function onConnect(Server $server, $fd, $reactorId) {
        
    }
    
    public function onReceive(Server $server, $fd, $reactorId, $data) {
        $this->dispatch('Receive', $fd, $reactorId, $data);
    }
    
    public function onClose(Server $server, $fd, $reactorId) {
        
    }
}
