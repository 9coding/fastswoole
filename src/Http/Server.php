<?php

namespace Fastapi\Http;

use Fastapi\Core;
use Fastapi\Exception\ServerException;
use League\Pipeline\Pipeline;
use Fastapi\Middleware\BaseMiddleware;
use Swoole\Process;

class Server {

    private $config;
    
    private $log;
    
    private $server;
    
    private $masterId = '';
    
    private $lang;

    public function __construct($config) {
        $this->config = $config;
        $this->lang = Core::$container['lang']->get('server');
        $this->log = Core::$container['log'];
        $this->log->pushHandler($this->config['log_file']);
    }
    
    public function execute($command = '', $option = '') {
        if (file_exists($this->config['pid_file'])) {
            $this->masterId = file_get_contents($this->config['pid_file']);
        }
        switch ($command) {
            case 'start':
                return $this->start($option);
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
            $this->log->addInfo($this->lang['already_start']);
            return false;
        }
        if ($option == '-d') {
            $this->config['daemonize'] = true;
        }
        $this->log->addInfo($this->lang['start_success']);
        $this->run();
    }
    
    private function stop() {
        $isMasterAlive = $this->detectMaster();
        if (!$isMasterAlive) {
            $this->log->addInfo($this->lang['already_stop']);
            return false;
        }
        $this->log->addInfo($this->lang['doing_stop']);
        Process::kill($this->masterId);
        $timeout = 5;
        $start_time = time();
        while (1) {
            $isMasterAlive = $this->detectMaster();
            if ($isMasterAlive) {
                if (time() - $start_time >= $timeout) {
                    $this->log->addInfo($this->lang['stop_failed']);
                    exit;
                }
                usleep(10000);
                continue;
            }
            $this->log->addInfo($this->lang['stop_success']);
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
        $this->log->addInfo($this->lang['reload_success']);
        return true;
    }
    
    private function run() {
        if ($this->config['use_https']) {
            $this->server = new \swoole_http_server($this->config['monitor_ip'], $this->config['monitor_port'], SWOOLE_PROCESS, SWOOLE_SOCK_TCP|SWOOLE_SSL);
            $this->config['ssl_cert_file'] = CONFIG_DIR.'/ssl/ssl.crt';
            $this->config['ssl_key_file'] = CONFIG_DIR.'/ssl/ssl.key';
            $this->config['open_http2_protocol'] = true;
        } else {
            $this->server = new \swoole_http_server($this->config['monitor_ip'], $this->config['monitor_port'], SWOOLE_PROCESS);
        }
        $this->server->set($this->config);
        $this->server->on('Start', array($this, 'onMasterStart'));
	$this->server->on('Shutdown', array($this, 'onShutdown'));
	$this->server->on('ManagerStart', array($this, 'onManagerStart'));
        $this->server->on('WorkerStart', array($this, 'onWorkerStart'));
        $this->server->on('WorkerError', array($this, 'onWorkerError'));
        $this->server->on('Task', array($this, 'onTask'));
        $this->server->on('Finish', array($this, 'onFinish'));
        $this->server->on('Request', array($this, 'onRequest'));
        $this->server->start();
    }
    
    public function onMasterStart(\swoole_server $server) {
        swoole_set_process_name('master process ' . $this->config['process_name']);
    }
    
    public function onShutdown(\swoole_server $server) {
        unlink($this->config['pid_file']);
    }
    
    public function onManagerStart(\swoole_server $server) {
        swoole_set_process_name('manager process ' . $this->config['process_name']);
    }
    
    public function onWorkerStart(\swoole_server $server, int $worker_id) {
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
    
    public function onWorkerError(\swoole_server $server, int $worker_id, int $worker_pid, int $exit_code, int $signal) {
        $this->log->addInfo('worker_id : ' . $worker_id . '; worker_pid : ' . $worker_pid . '; exit_code : ' . $exit_code . '; signal : ' . $signal);
    }
    
    public function onTask(\swoole_server $server, int $task_id, int $src_worker_id, $data) {
        
    }
    
    public function onFinish(\swoole_server $server, int $task_id, $data) {
        
    }
    
    public function onRequest(\swoole_http_request $request, \swoole_http_response $response) {
        if($request->server['path_info'] == '/favicon.ico' || $request->server['request_uri'] == '/favicon.ico') {
            return $response->end();
       	}
        try {
            $pipeline = new Pipeline();
            $registeredMiddlewares = BaseMiddleware::fetchMiddleware();
            foreach ($registeredMiddlewares as $middleware) {
                $pipeline = $pipeline->pipe($middleware);
            }
            $middlewareResult = $pipeline->process($request);
            Core::$container['route']->dispatch($request, $response);
        } catch (ServerException $error) {
            $response->status($error->getCode());
            $response->end($error->response());
        } catch (\Exception $exc) {
            $response->status($exc->getCode());
            $response->end($exc->getMessage());
        }
    }
}
