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
        'max_connnect' => 50,
    ],
    'redis' => [
        'host' => '127.0.0.1',
        'port' => 6379,
        'db' => 1,
        'password' => ''
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
