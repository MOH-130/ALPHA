<?php

$dbUrl = env('DATABASE_URL');
$mysqlConfig = [
    'driver' => 'mysql',
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => (int)env('DB_PORT', 3306),
    'database' => env('DB_DATABASE', 'ecommerce_store'),
    'username' => env('DB_USERNAME', 'root'),
    'password' => env('DB_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'strict' => true,
    'engine' => 'InnoDB',
    'ssl' => env('DB_SSL', false),
];

if (!empty($dbUrl)) {
    $parsed = parse_url($dbUrl);
    if (!empty($parsed['host'])) {
        $mysqlConfig['host'] = $parsed['host'];
        $mysqlConfig['port'] = (int)($parsed['port'] ?? 3306);
        $mysqlConfig['username'] = urldecode($parsed['user'] ?? '');
        $mysqlConfig['password'] = urldecode($parsed['pass'] ?? '');
        $mysqlConfig['database'] = ltrim($parsed['path'] ?? '', '/');
        $mysqlConfig['ssl'] = true;
    }
}

return [
    'default' => env('DB_CONNECTION', !empty($dbUrl) ? 'mysql' : 'sqlite'),

    'connections' => [
        'sqlite' => [
            'driver' => 'sqlite',
            'database' => __DIR__ . '/../storage/data/database.sqlite',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ],

        'mysql' => $mysqlConfig,
    ],
];

