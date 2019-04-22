<?php

namespace FastSwoole;

use Monolog\Logger as Monologger;
use Monolog\Handler\StreamHandler;

class Logger {
    
    private $handle;

    public function __construct($log) {
        $this->handle = new Monologger($log);
    }
    
    public function pushHandler($logfile) {
        $this->handle->pushHandler(new StreamHandler($logfile, Monologger::ERROR));
    }
    
    public function __call($name, $arguments) {
        $content = ($arguments[0]) ?? '';
        $this->handle->$name($content);
    }
}
