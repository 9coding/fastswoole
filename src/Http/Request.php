<?php

namespace FastSwoole\Http;

class Request {

    public $controller = '';

    public $action = '';

    private $serverParams = [];

    private $getParams = [];

    private $postParams = [];

    public $method = '';

    public function __construct($request, $otherParam) {
        $this->serverParams = $request->server;
        $this->method       = $this->serverParams['request_method'];
        $this->getParams     = $request->get;
        $this->postParams    = $request->post;
        if (is_array($otherParam)) {
            foreach ($otherParam as $paramName => $paramValue) {
                $this->getParams[$paramName] = $paramValue;
            }
        }
    }

    public function __get($name = '') {
        return $this->$name;
    }

    public function __set($name = '', $value = '') {
        $this->$name = $value;
    }

    public function get($key = '', $default = '') {
        if (!$key) {
            $request = $this->getParams;
            foreach ($request as &$v) {
                $v = htmlspecialchars($v);
            }
            return $request;
        }
        if (!isset($this->getParams[$key])) {
            return '';
        }
        if (empty($this->getParams[$key])) {
            return $default;
        }
        return htmlspecialchars($this->getParams[$key]);
    }

    public function post($key = '', $default = '') {
        if (!$key) {
            return $this->postParams;
        }
        if (!isset($this->postParams[$key])) {
            return '';
        }
        if (empty($this->postParams[$key])) {
            return $default;
        }
        return $this->postParams[$key];
    }

    public function all() {
        $request = array_merge($this->postParams, $this->getParams);
        foreach ($request as &$v) {
            $v = htmlspecialchars($v);
        }
        return $request;
    }

    public function server($key = '') {
        if (isset($this->serverParams[$key])) {
            return $this->serverParams[$key];
        }
        return '';
    }
}
