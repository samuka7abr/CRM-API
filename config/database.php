<?php

return [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', env('DB_NAME')),
            'username' => env('DB_USERNAME', env('DB_USER')),
            'password' => env('DB_PASSWORD', env('DB_PASS')),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],
        
        // Driver fake para quando não quiser usar banco
        'array' => [
            'driver' => 'array',
        ],
    ],
];
