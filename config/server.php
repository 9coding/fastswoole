<?php

return [
    'http' => [
        'monitor_ip' => '0.0.0.0',
        'monitor_port' => 9527,
        'worker_num' => 2,
        'task_worker_num' => 4,
        'daemonize' => 0,
        'max_request' => 5000,
        'open_cpu_affinity' => 1,
        'open_tcp_nodelay' => 1,
        'dispatch_mode' => 3,
        'chroot' => APP_DIR,
        'user' => 'www-data',
        'group' => 'www-data',
        'log_file' => TEMP_DIR . '/swoole.log',
        'pid_file' => TEMP_DIR . '/server.pid',
        'use_https' => 0,
        'process_name' => 'fastswoole',
        'document_root' => ROOT_DIR.'/public',
        'enable_static_handler' => true,
        'max_coroutine' => 3000,//根据实际业务的压测结果设置该值，默认为3000
    ],

    'websocket' => [
        
    ]
];
