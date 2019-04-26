<?php

namespace FastSwoole;

use FastSwoole\Core;
use Swoole\Process;
use Swoole\Server as SwooleServer;

class Server {

    public $config;
    
    public $masterId = '';
    
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
    
    public function setCallback() {
        $this->server->set($this->config);
        $this->server->on('Start', [$this, 'onMasterStart']);
	$this->server->on('Shutdown', [$this, 'onShutdown']);
	$this->server->on('ManagerStart', [$this, 'onManagerStart']);
        $this->server->on('WorkerStart', [$this, 'onWorkerStart']);
        $this->server->on('WorkerError', [$this, 'onWorkerError']);
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
}
