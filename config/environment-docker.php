<?php

return [
    'database' => [
        'connection' => [
            'driver'    => 'pdo_mysql',
            'host'      => 'database',
            'dbname'    => 'sandstone',
            'user'      => 'root',
            'password'  => 'root',
            'charset'   => 'utf8',
        ],
        'orm' => [
            'auto_generate_proxies' => true,
        ],
    ],
    'websocket' => [
        'server' => [
            'bind' => '0.0.0.0',
            'port' => 25569,
        ],
    ],
    'push' => [
        'enabled' => true,
        'server' => [
            'bind' => '0.0.0.0',
            'host' => 'websocket-server',
            'port' => 5555,
        ],
    ],
    'cors' => [
        'cors.allowOrigin' => '*',
    ],
];
