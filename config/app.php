<?php

return [
    'http' => [
        'default_controller' => 'index',
        'default_action' => 'index',
        'default_language' => 'zh-cn',
        'debug_mode' => false,
        'middleware' => [
            'ThrottleRequests' => false
        ],
        'service' => [
            Fastapi\Service\RouteProvider::class,
            Fastapi\Service\HttpServerProvider::class,
            Fastapi\Service\LanguageProvider::class,
        ]
    ],

    'websocket' => [
        
    ]
];
