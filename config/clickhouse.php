<?php

return [

    /*
    |--------------------------------------------------------------------------
    | ClickHouse Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure a connection to connect to the ClickHouse
    | database and specify additional configurations
    |
    */

    'config' => [
        'host' => env('CLICKHOUSE_HOST', 'localhost'),
        'port' => env('CLICKHOUSE_PORT', 8123),
        'username' => env('CLICKHOUSE_USER', 'default'),
        'password' => env('CLICKHOUSE_PASSWORD', ''),
        'options' => [
            'database' => env('CLICKHOUSE_DATABASE', 'default'),
            'timeout' => 1,
            'connectTimeOut' => 2,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | ClickHouse Migrations
    |--------------------------------------------------------------------------
    |
    | ClickHouse settings for working with migrations
    |
    */

    'migrations' => [
        'table' => env('CLICKHOUSE_MIGRATION_TABLE', 'migrations'),
        'path' => database_path('clickhouse-migrations'),
    ],

    /*
    |--------------------------------------------------------------------------
    | ClickHouse Stubs
    |--------------------------------------------------------------------------
    |
    */

    'stubs' => [
       // 'default' => base_path('stubs/file.stub),
    ],

    'handlers' => [
       // 'App\Clickhouse\Handlers\MyHandler',
    ],
];
