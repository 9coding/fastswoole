<?php

return [
    'mysql' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'user' => 'root',
        'password' => '820819',
        'database' => 'chatroom',
        'charset' => 'utf8mb4',
        'timeout' => 5,
        'max_connnect' => 20,//如果不设置此项或者将其设置为0时不使用连接池，下同
    ],
    'redis' => [
        'host' => '127.0.0.1',
        'port' => 6379,
        'db' => 0,
        'password' => '',
        'connect_timeout' => 2,
        'timeout' => 5
    ],
    'mongodb' => [
        'server' => '127.0.0.1',
        'port' => 27017,
        'dbname' => 'fastswoole',
        'user' => '',
        'password' => '',
        'options' => []
    ]
];
