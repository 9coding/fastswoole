<?php

return [
    'mysql' => [
        'mode'=>'base',
        'host' => 'localhost',
        'port' => 3306,
        'user' => 'root',
        'password' => '820819',
        'database' => 'fastcrawler',
        'charset' => 'utf8mb4',
        'maxConnnectNum' => 10,
        'test' => [
            'host' => '192.168.112.55',
            'maxConnnectNum' => 5
        ],
        'production' => [
            'host' => '192.168.112.58',
            'maxConnnectNum' => 20
        ]
    ],
    'redis' => [
        'host' => 'localhost',
        'port' => 6379,
        'db' => 1,
        'password' => ''
    ],
    'mongodb' => [
        'server' => 'localhost',
        'port' => 27017,
        'dbname' => 'fastapi',
        'user' => '',
        'password' => '',
        'options' => []
    ]
];
