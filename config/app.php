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
            FastSwoole\Service\RouteProvider::class,
            FastSwoole\Service\HttpServerProvider::class,
            FastSwoole\Service\LanguageProvider::class,
        ]
    ],

    'websocket' => [
        'middleware' => [
            'WordFilter' => ['fuck','shit']
        ],
        
        'service' => [
            FastSwoole\Service\WebsocketProvider::class,
        ]
    ]
];
