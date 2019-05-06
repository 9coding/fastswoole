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
        'log_file' => TEMP_DIR . '/swoole_http.log',
        'pid_file' => TEMP_DIR . '/server_http.pid',
        'use_https' => 0,
        'process_name' => 'fastswoole_http',
        'document_root' => ROOT_DIR.'/public',
        'enable_static_handler' => true,
        'max_coroutine' => 5000,//根据实际业务的压测结果设置该值，默认为3000
        'task_enable_coroutine' => true,
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
        'log_file' => TEMP_DIR . '/swoole_websocket.log',
        'pid_file' => TEMP_DIR . '/server_websocket.pid',
        'process_name' => 'fastswoole_websocket',
        'task_enable_coroutine' => true,
    ],
    
    'tcp' => [
        'monitor_ip' => '0.0.0.0',
        'monitor_port' => 9595,
        'worker_num' => 2,
//        'task_worker_num' => 2,//开启此项需要websocket设置ontask回调
        'daemonize' => 0,
        'max_request' => 20,
        'open_cpu_affinity' => 1,
        'open_tcp_nodelay' => 1,
        'dispatch_mode' => 3,
        'log_file' => TEMP_DIR . '/swoole_tcp.log',
        'pid_file' => TEMP_DIR . '/server_tcp.pid',
        'process_name' => 'fastswoole_tcp',
        'package_max_length' => 8192,
        'open_eof_split' => true,
        'package_eof' => "\r\n",
        'task_enable_coroutine' => true,
    ],
    
    'udp' => [
        'monitor_ip' => '0.0.0.0',
        'monitor_port' => 9596,
        'worker_num' => 2,
//        'task_worker_num' => 2,//开启此项需要websocket设置ontask回调
        'daemonize' => 0,
        'max_request' => 20,
        'open_cpu_affinity' => 1,
        'open_tcp_nodelay' => 1,
        'dispatch_mode' => 3,
        'log_file' => TEMP_DIR . '/swoole_udp.log',
        'pid_file' => TEMP_DIR . '/server_udp.pid',
        'process_name' => 'fastswoole_udp',
        'task_enable_coroutine' => true,
    ]
];
