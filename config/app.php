<?php

return [
    'default_module' => 'v1',
    'default_controller' => 'index',
    'default_action' => 'index',
    'default_language' => 'zh-cn',
    
    'debug_mode' => false,
    
    'middleware' => [
        'ThrottleRequests' => false
    ],
    
    'service' => [
        Fastapi\Service\ConfigProvider::class,
    ]
];
