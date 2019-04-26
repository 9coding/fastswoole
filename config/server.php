<?php

return [
    'http' => [
        'monitor_ip' => '0.0.0.0',
        'monitor_port' => 9527,
        'worker_num' => 2,
        'task_worker_num' => 4,
        'daemonize' => 0,
        'max_request' => 10000,
        'open_cpu_affinity' => 1,
        'open_tcp_nodelay' => 1,
        'dispatch_mode' => 3,
        'chroot' => APP_DIR,
        'user' => 'www-data',
        'group' => 'www-data',
        'log_file' => TEMP_DIR . '/swoole.log',
        'pid_file' => TEMP_DIR . '/server.pid',
        'use_https' => 0,
        'process_name' => 'fastswoole_http',
        'document_root' => ROOT_DIR.'/public',
        'enable_static_handler' => true,
        'max_coroutine' => 5000,//根据实际业务的压测结果设置该值，默认为3000
    ],

    'websocket' => [
        'monitor_ip' => '0.0.0.0',
        'monitor_port' => 9528,
        'worker_num' => 2,
//        'task_worker_num' => 2,//开启此项需要websocket设置ontask回调
        'daemonize' => 0,
        'max_request' => 5000,
        'open_cpu_affinity' => 1,
        'open_tcp_nodelay' => 1,
        'dispatch_mode' => 3,
        'log_file' => TEMP_DIR . '/swoole.log',
        'pid_file' => TEMP_DIR . '/server.pid',
        'process_name' => 'fastswoole_websocket',
    ],
    
    'tcp' => [
        'monitor_ip' => '0.0.0.0',
        'monitor_port' => 9595,
        'worker_num' => 2,
//        'task_worker_num' => 2,//开启此项需要websocket设置ontask回调
        'daemonize' => 0,
        'max_request' => 5000,
        'open_cpu_affinity' => 1,
        'open_tcp_nodelay' => 1,
        'dispatch_mode' => 3,
        'log_file' => TEMP_DIR . '/swoole.log',
        'pid_file' => TEMP_DIR . '/server.pid',
        'process_name' => 'fastswoole_tcp',
    ],
    
    'udp' => [
        'monitor_ip' => '0.0.0.0',
        'monitor_port' => 9596,
        'worker_num' => 2,
//        'task_worker_num' => 2,//开启此项需要websocket设置ontask回调
        'daemonize' => 0,
        'max_request' => 5000,
        'open_cpu_affinity' => 1,
        'open_tcp_nodelay' => 1,
        'dispatch_mode' => 3,
        'log_file' => TEMP_DIR . '/swoole.log',
        'pid_file' => TEMP_DIR . '/server.pid',
        'process_name' => 'fastswoole_udp',
    ]
];
