<?php

namespace application\websocket;

use FastSwoole\Websocket\Controller;

class Message extends Controller {

    public function execute() {
        $frame = $this->param;
        $framedata = explode('|+|', $frame->data);
        echo "receive from {$frame->fd}:$framedata[0],opcode:{$frame->opcode},fin:{$frame->finish}\n";
        if (isset($framedata[1]) && $framedata[1]) {
            $this->server->push($framedata[1], json_encode(array('event'=>'message','type'=>'other','from'=>$frame->fd,'content'=>$framedata[0],'target'=>'你')));
            $this->server->push($frame->fd, json_encode(array('event'=>'message','type'=>'self','from'=>$frame->fd,'content'=>$framedata[0],'target'=>$framedata[1])));
        } else {
            foreach ($this->server->connections as $fd) {
                if ($frame->fd != $fd) {
                    $this->server->push($fd, json_encode(array('event'=>'message','type'=>'other','from'=>$frame->fd,'content'=>$framedata[0],'target'=>'所有人')));
                }
            }
            $this->server->push($frame->fd, json_encode(array('event'=>'message','type'=>'self','from'=>$frame->fd,'content'=>$framedata[0],'target'=>'所有人')));
        }
    }
}
