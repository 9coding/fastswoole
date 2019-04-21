<?php

namespace FastSwoole\Exception;

use Exception;
use Fastapi\Core;

class ServerException extends Exception {

    private $httpCode = [
        200 => 'OK',
        400 => 'Bad Request',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Forbidden Method',
        500 => 'Internet Server Error',
        503 => 'Service Unavailable'
    ];
    
    public function __construct($httpcode = 200, $msg = '') {
        $this->code = $httpcode;
        if (empty($msg)) {
            $this->message = $this->httpCode[$httpcode];
        } else {
            $this->message = $msg . ' ' . $this->httpCode[$httpcode];
        }
    }
    
    public function response() {
        $info = [
            'code'    => $this->getCode(),
            'message' => $this->getMessage(),
        ];
        $debug_mode = Core::$app['config']->get('app.http.debug_mode');
        if ($debug_mode) {
            $info['infomations'] = [
                'file'  => $this->getFile(),
                'line'  => $this->getLine(),
                'trace' => $this->getTrace(),
            ];
        }
        return json_encode($info, JSON_UNESCAPED_UNICODE);
    }
}
