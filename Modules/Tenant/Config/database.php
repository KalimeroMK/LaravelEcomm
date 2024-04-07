<?php

return [
    'connections' => [
        'owner' => [
            'driver' => 'mysql',
            'url' => env('OWNER_DATABASE_URL'),
            'host' => env('OWNER_DB_HOST', '127.0.0.1'),
            'port' => env('OWNER_DB_PORT', '3306'),
            'database' => env('OWNER_DB_DATABASE', 'forge'),
            'username' => env('OWNER_DB_USERNAME', 'forge'),
            'password' => env('OWNER_DB_PASSWORD', ''),
            'unix_socket' => env('OWNER_DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

    ],
];
