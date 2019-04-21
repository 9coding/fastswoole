<?php

namespace FastSwoole;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Logger {
    
    private $handle;

    public function __construct($log) {
        $this->handle = new Logger($log);
    }
    
    public function pushHandler($logfile) {
        $this->handle->pushHandler(new StreamHandler($logfile, Logger::ERROR));
    }
    
    public function __call($name, $arguments) {
        $content = ($arguments[0]) ?? '';
        $this->handle->$name($content);
    }
}
