<?php

namespace FastSwoole\Http;

use FastSwoole\Core;
use FastSwoole\Exception\ServerException;
use FastSwoole\Server as FastSwooleServer;
use League\Pipeline\Pipeline;
use Swoole\Http\Server as HttpServer;
use Swoole\Http\Request as HttpRequest;
use Swoole\Http\Response as HttpResponse;
use Swoole\Server as SwooleServer;

class Server extends FastSwooleServer {
    
    public $server;

    public function __construct() {
        $this->config = Core::$app['config']->get('server.http');
    }
    
    public function run() {
        if ($this->config['use_https']) {
            $this->server = new HttpServer($this->config['monitor_ip'], $this->config['monitor_port'], SWOOLE_PROCESS, SWOOLE_SOCK_TCP|SWOOLE_SSL);
            $this->config['ssl_cert_file'] = CONFIG_DIR.'/ssl/ssl.crt';
            $this->config['ssl_key_file'] = CONFIG_DIR.'/ssl/ssl.key';
            $this->config['open_http2_protocol'] = true;
        } else {
            $this->server = new HttpServer($this->config['monitor_ip'], $this->config['monitor_port'], SWOOLE_PROCESS);
        }
        $this->server->set($this->config);
        $this->server->on('Start', [$this, 'onMasterStart']);
	$this->server->on('Shutdown', [$this, 'onShutdown']);
	$this->server->on('ManagerStart', [$this, 'onManagerStart']);
        $this->server->on('WorkerStart', [$this, 'onWorkerStart']);
        $this->server->on('WorkerError', [$this, 'onWorkerError']);
        $this->server->on('Task', [$this, 'onTask']);
        $this->server->on('Finish', [$this, 'onFinish']);
        $this->server->on('Request', [$this, 'onRequest']);
        $this->server->start();
    }
    
    public function onTask(SwooleServer $server, int $task_id, int $src_worker_id, $data) {
        
    }
    
    public function onFinish(SwooleServer $server, int $task_id, $data) {
        
    }
    
    public function onRequest(HttpRequest $request, HttpResponse $response) {
        if($request->server['path_info'] == '/favicon.ico' || $request->server['request_uri'] == '/favicon.ico') {
            return $response->end();
       	}
        try {
            $pipeline = new Pipeline();
            $registeredMiddlewares = Core::$app['middleware']->fetchMiddleware();
            foreach ($registeredMiddlewares as $middleware) {
                $pipeline = $pipeline->pipe($middleware);
            }
            $middlewareResult = $pipeline->process($request);
            Core::$app['route']->dispatch($request, $response);
        } catch (ServerException $error) {
            $response->status($error->getCode());
            $response->end($error->response());
        } catch (\Exception $exc) {
            $response->status($exc->getCode());
            $excMsg = 'File '.$exc->getFile().' in line '.$exc->getLine().'. Error is '.$exc->getMessage();
            $response->end($excMsg);
        }
    }
}
