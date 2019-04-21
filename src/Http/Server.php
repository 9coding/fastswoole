<?php

namespace FastSwoole\Http;

use FastSwoole\Core;
use FastSwoole\Exception\ServerException;
use League\Pipeline\Pipeline;
use Swoole\Process;
use Swoole\Http\Server as HttpServer;
use Swoole\Http\Request as HttpRequest;
use Swoole\Http\Response as HttpResponse;
use Swoole\Server as SwooleServer;

class Server {

    private $config;
    
    private $server;
    
    private $masterId = '';

    public function __construct($config) {
        $this->config = Core::$app['config']->get('server.http');
    }
    
    public function execute($command = '') {
        if (file_exists($this->config['pid_file'])) {
            $this->masterId = file_get_contents($this->config['pid_file']);
        }
        switch ($command) {
            case 'start':
                return $this->start();
                break;
            case 'startd':
                return $this->start('-d');
                break;
            case 'restart':
                return $this->restart();
                break;
            case 'stop':
                return $this->stop();
                break;
            case 'reload':
                return $this->reload();
                break;
            default:
                return false;
                break;
        }
    }
    
    private function detectMaster() {
        return $this->masterId && Process::kill($this->masterId, 0);
    }
    
    private function start($option = '') {
        $isMasterAlive = $this->detectMaster();
        if ($isMasterAlive) {
            Core::$app['log']->addInfo('服务器已经启动，请勿重复启动');
            return false;
        }
        if ($option == '-d') {
            $this->config['daemonize'] = true;
        }
        Core::$app['log']->addInfo('服务器启动成功');
        $this->run();
    }
    
    private function stop() {
        $isMasterAlive = $this->detectMaster();
        if (!$isMasterAlive) {
            Core::$app['log']->addInfo('服务器没有启动');
            return false;
        }
        Core::$app['log']->addInfo('服务器正在停止，请稍后...');
        Process::kill($this->masterId);
        $timeout = 5;
        $start_time = time();
        while (1) {
            $isMasterAlive = $this->detectMaster();
            if ($isMasterAlive) {
                if (time() - $start_time >= $timeout) {
                    Core::$app['log']->addInfo('服务器停止失败，请重试');
                    exit;
                }
                usleep(10000);
                continue;
            }
            Core::$app['log']->addInfo('服务器停止成功');
            break;
        }
        return true;
    }
    
    private function restart() {
        $stopResult = $this->stop();
        if ($stopResult) {
            $this->config['daemonize'] = true;
            return $this->start();
        } else {
            return false;
        }
    }
    
    private function reload() {
        Process::kill($this->masterId, SIGUSR1);
        Core::$app['log']->addInfo('服务器重载成功');
        return true;
    }
    
    private function run() {
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
    
    public function onMasterStart(SwooleServer $server) {
        swoole_set_process_name('master process ' . $this->config['process_name']);
    }
    
    public function onShutdown(SwooleServer $server) {
        unlink($this->config['pid_file']);
    }
    
    public function onManagerStart(SwooleServer $server) {
        swoole_set_process_name('manager process ' . $this->config['process_name']);
    }
    
    public function onWorkerStart(SwooleServer $server, int $worker_id) {
        if(function_exists('apc_clear_cache')){
            apc_clear_cache();
        }
        if(function_exists('opcache_reset')){
            opcache_reset();
        }
        if ($worker_id >= $server->setting['worker_num']) {
            swoole_set_process_name('task worker process '.$this->config['process_name']);
        } else {
            swoole_set_process_name('event worker process '.$this->config['process_name']);
        }
    }
    
    public function onWorkerError(SwooleServer $server, int $worker_id, int $worker_pid, int $exit_code, int $signal) {
        Core::$app['log']->addInfo('worker_id : ' . $worker_id . '; worker_pid : ' . $worker_pid . '; exit_code : ' . $exit_code . '; signal : ' . $signal);
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
            $response->end($exc->getMessage());
        }
    }
}
