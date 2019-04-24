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
        $this->server->set($this->config);
        $this->server->on('Start', [$this, 'onMasterStart']);
	$this->server->on('Shutdown', [$this, 'onShutdown']);
	$this->server->on('ManagerStart', [$this, 'onManagerStart']);
        $this->server->on('WorkerStart', [$this, 'onWorkerStart']);
        $this->server->on('WorkerError', [$this, 'onWorkerError']);
        $this->server->on('Open', [$this, 'onOpen']);
        $this->server->on('Message', [$this, 'onMessage']);
        $this->server->on('Close', [$this, 'onClose']);
        $this->server->start();
    }
    
    private function dispatch($target, $data) {
        $result = false;
        $className = '\application\\websocket\\'.$target;
        if (class_exists($className)) {
            $reflaction = new \ReflectionClass($className);
            if ($reflaction->hasMethod('execute')) {
                $methodParams = $this->analyzeParameter($className, 'execute');
                array_unshift($methodParams, $data);
                $controller = new $className();
                $result = call_user_func_array(array($controller, 'execute'), $methodParams);
            }
        }
        return $result;
    }

    public function onOpen(WebSocketServer $server, $request) {
        $result = $this->dispatch('Open', $request);
        if ($result === false) {
            $server->disconnect($request->fd);
        } else {
            $server->push($request->fd, json_encode(array('event'=>'open','type'=>'other_user','target'=>$result,'self'=>$request->fd)));
        }
    }
    
    public function onMessage(WebSocketServer $server, $frame) {
        $pipeline = new Pipeline();
        $registeredMiddlewares = Core::$app['middleware']->fetchMiddleware();
        foreach ($registeredMiddlewares as $middleware) {
            $pipeline = $pipeline->pipe($middleware);
        }
        $frame->data = $pipeline->process($frame->data);
        $result = $this->dispatch('Message', $frame);
    }
    
    public function onClose(WebSocketServer $server, $closefd) {
        $result = $this->dispatch('Close', $closefd);
    }
}
