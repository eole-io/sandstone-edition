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
            'port' => 8482,
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
    'oauth' => [
        'scope' => [
            'id' => 'sandstone-scope',
            'description' => 'Sandstone scope.',
        ],
        'clients' => [
            'my-web-application' => [
                'name' => 'my-app-name',
                'id' => 'my-app',
                'secret' => 'my-app-secret',
            ],
        ],
    ],
];
