<?php

namespace application\websocket;

class Open {

    public function execute($request) {
        $userlist = [];
        foreach ($server->connections as $fd) {
            if ($request->fd != $fd) {
                $userlist[] = $fd;
                $server->push($fd, json_encode(array('event'=>'open','type'=>'new_user','target'=>$request->fd)));
            }
        }
        $userlist[] = $request->fd;
    }
}
