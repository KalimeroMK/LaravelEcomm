<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Elasticsearch Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure your Elasticsearch connection settings.
    |
    */

    'hosts' => [
        [
            'host' => env('ELASTICSEARCH_HOST', 'localhost'),
            'port' => env('ELASTICSEARCH_PORT', 9200),
            'scheme' => env('ELASTICSEARCH_SCHEME', 'http'),
            'user' => env('ELASTICSEARCH_USER', null),
            'pass' => env('ELASTICSEARCH_PASS', null),
        ]
    ],

    'index_prefix' => env('ELASTICSEARCH_INDEX_PREFIX', 'laravel_'),

    'indices' => [
        'products' => [
            'name' => 'products',
            'settings' => [
                'number_of_shards' => 1,
                'number_of_replicas' => 0,
                'analysis' => [
                    'analyzer' => [
                        'product_analyzer' => [
                            'type' => 'custom',
                            'tokenizer' => 'standard',
                            'filter' => ['lowercase', 'stop', 'snowball']
                        ]
                    ]
                ]
            ]
        ]
    ],

    'connection_timeout' => env('ELASTICSEARCH_CONNECTION_TIMEOUT', 10),
    'request_timeout' => env('ELASTICSEARCH_REQUEST_TIMEOUT', 30),

    'retry_on_conflict' => env('ELASTICSEARCH_RETRY_ON_CONFLICT', 3),
    'max_retries' => env('ELASTICSEARCH_MAX_RETRIES', 3),

    'logging' => [
        'enabled' => env('ELASTICSEARCH_LOGGING_ENABLED', false),
        'level' => env('ELASTICSEARCH_LOGGING_LEVEL', 'info'),
    ],

    'bulk' => [
        'batch_size' => env('ELASTICSEARCH_BULK_BATCH_SIZE', 100),
        'refresh_interval' => env('ELASTICSEARCH_BULK_REFRESH_INTERVAL', '1s'),
    ],
];
