<?php

namespace FastSwoole\Websocket;

use FastSwoole\Core;
use FastSwoole\Server as FastSwooleServer;
use League\Pipeline\Pipeline;
use Swoole\WebSocket\Server as WebSocketServer;
use Swoole\Server as SwooleServer;

class Server extends FastSwooleServer {
    
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

    public function onOpen(SwooleServer $server, $request) {
        $userlist = [];
        foreach ($server->connections as $fd) {
            if ($request->fd != $fd) {
                $userlist[] = $fd;
                $server->push($fd, json_encode(array('event'=>'open','type'=>'new_user','target'=>$request->fd)));
            }
        }
        $userlist[] = $request->fd;
        $server->push($request->fd, json_encode(array('event'=>'open','type'=>'other_user','target'=>$userlist,'self'=>$request->fd)));
    }
    
    public function onMessage(SwooleServer $server, $frame) {
        $framedata = explode('|+|', $frame->data);
        echo "receive from {$frame->fd}:$framedata[0],opcode:{$frame->opcode},fin:{$frame->finish}\n";
        if (isset($framedata[1]) && $framedata[1]) {
            $server->push($framedata[1], json_encode(array('event'=>'message','type'=>'other','from'=>$frame->fd,'content'=>$framedata[0],'target'=>'你')));
            $server->push($frame->fd, json_encode(array('event'=>'message','type'=>'self','from'=>$frame->fd,'content'=>$framedata[0],'target'=>$framedata[1])));
        } else {
            foreach ($server->connections as $fd) {
                if ($frame->fd != $fd) {
                    $server->push($fd, json_encode(array('event'=>'message','type'=>'other','from'=>$frame->fd,'content'=>$framedata[0],'target'=>'所有人')));
                }
            }
            $server->push($frame->fd, json_encode(array('event'=>'message','type'=>'self','from'=>$frame->fd,'content'=>$framedata[0],'target'=>'所有人')));
        }
    }
    
    public function onClose(SwooleServer $server, $closefd) {
        foreach ($server->connections as $fd) {
            if ($closefd != $fd) {
                $server->push($fd, json_encode(array('event'=>'close','type'=>'quit','target'=>$closefd)));
            }
        }
    }
}
