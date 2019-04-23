<?php

namespace application\websocket;

class Close {

    public function execute($closefd) {
        foreach ($server->connections as $fd) {
            if ($closefd != $fd) {
                $server->push($fd, json_encode(array('event'=>'close','type'=>'quit','target'=>$closefd)));
            }
        }
    }
}
