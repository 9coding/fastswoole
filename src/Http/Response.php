<?php

namespace FastSwoole\Http;


class Response {

    private $response;
    
    private $header = [];
    
    private $httpCode = 200;
    
    public function __construct($response) {
        $this->response = $response;
    }
    
    public function setHeader($key, $value) {
        $this->header[$key] = $value;
    }
    
    public function setHttpCode($code) {
        $this->httpCode = $code;
    }
    
    public function sendJson($data = '', $code = 200) {
        $msg = json_encode(['result' => $data], JSON_UNESCAPED_UNICODE);
        $this->setHeader('Content-Type', 'application/json; Charset=utf-8');
        $this->setHttpCode($code);
        $this->send($msg);
    }
    
    public function sendText($data = 'OK', $code = 200) {
        $this->setHeader('Content-Type', 'text/html; Charset=utf-8');
        $this->setHttpCode($code);
        $this->send($data);
    }
    
    public function send($data) {
        $this->response->status($this->httpCode);
        foreach ($this->header as $key => $value) {
            $this->response->header($key, $value);
        }
        $this->response->end($data);
    }
}
