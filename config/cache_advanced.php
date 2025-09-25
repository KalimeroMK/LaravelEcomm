<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Advanced Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure advanced caching settings for your application.
    |
    */

    'default_ttl' => env('CACHE_DEFAULT_TTL', 3600), // 1 hour
    'short_ttl' => env('CACHE_SHORT_TTL', 900),      // 15 minutes
    'long_ttl' => env('CACHE_LONG_TTL', 86400),      // 24 hours
    'very_long_ttl' => env('CACHE_VERY_LONG_TTL', 604800), // 7 days

    /*
    |--------------------------------------------------------------------------
    | Cache Tags Configuration
    |--------------------------------------------------------------------------
    |
    | Define cache tags for different content types.
    |
    */

    'tags' => [
        'products' => 'products',
        'categories' => 'categories',
        'brands' => 'brands',
        'users' => 'users',
        'orders' => 'orders',
        'settings' => 'settings',
        'analytics' => 'analytics',
        'search' => 'search',
        'api' => 'api',
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Warming Configuration
    |--------------------------------------------------------------------------
    |
    | Configure automatic cache warming for critical data.
    |
    */

    'warming' => [
        'enabled' => env('CACHE_WARMING_ENABLED', true),
        'critical_data' => [
            'products:featured',
            'products:latest',
            'categories:tree',
            'brands:active',
            'settings:all',
        ],
        'warm_up_interval' => env('CACHE_WARM_UP_INTERVAL', 3600), // 1 hour
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Compression Configuration
    |--------------------------------------------------------------------------
    |
    | Configure cache compression for large data sets.
    |
    */

    'compression' => [
        'enabled' => env('CACHE_COMPRESSION_ENABLED', true),
        'threshold' => env('CACHE_COMPRESSION_THRESHOLD', 1024), // 1KB
        'algorithm' => env('CACHE_COMPRESSION_ALGORITHM', 'gzip'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Invalidation Configuration
    |--------------------------------------------------------------------------
    |
    | Configure automatic cache invalidation strategies.
    |
    */

    'invalidation' => [
        'model_events' => [
            'created' => true,
            'updated' => true,
            'deleted' => true,
        ],
        'tag_based' => true,
        'time_based' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Monitoring Configuration
    |--------------------------------------------------------------------------
    |
    | Configure cache performance monitoring.
    |
    */

    'monitoring' => [
        'enabled' => env('CACHE_MONITORING_ENABLED', true),
        'log_misses' => env('CACHE_LOG_MISSES', false),
        'log_hits' => env('CACHE_LOG_HITS', false),
        'performance_threshold' => env('CACHE_PERFORMANCE_THRESHOLD', 100), // ms
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Strategies
    |--------------------------------------------------------------------------
    |
    | Define caching strategies for different content types.
    |
    */

    'strategies' => [
        'products' => [
            'ttl' => 3600, // 1 hour
            'tags' => ['products'],
            'compression' => true,
            'warming' => true,
        ],
        'categories' => [
            'ttl' => 86400, // 24 hours
            'tags' => ['categories'],
            'compression' => false,
            'warming' => true,
        ],
        'brands' => [
            'ttl' => 86400, // 24 hours
            'tags' => ['brands'],
            'compression' => false,
            'warming' => true,
        ],
        'users' => [
            'ttl' => 1800, // 30 minutes
            'tags' => ['users'],
            'compression' => true,
            'warming' => false,
        ],
        'orders' => [
            'ttl' => 900, // 15 minutes
            'tags' => ['orders'],
            'compression' => true,
            'warming' => false,
        ],
        'analytics' => [
            'ttl' => 300, // 5 minutes
            'tags' => ['analytics'],
            'compression' => true,
            'warming' => false,
        ],
        'search' => [
            'ttl' => 1800, // 30 minutes
            'tags' => ['search'],
            'compression' => true,
            'warming' => false,
        ],
        'api' => [
            'ttl' => 600, // 10 minutes
            'tags' => ['api'],
            'compression' => true,
            'warming' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Keys Configuration
    |--------------------------------------------------------------------------
    |
    | Define cache key patterns for different content types.
    |
    */

    'keys' => [
        'products' => [
            'featured' => 'products:featured',
            'latest' => 'products:latest',
            'deals' => 'products:deals',
            'by_category' => 'products:category:{id}',
            'by_brand' => 'products:brand:{id}',
            'search' => 'products:search:{query}',
            'detail' => 'products:detail:{id}',
        ],
        'categories' => [
            'tree' => 'categories:tree',
            'active' => 'categories:active',
            'by_parent' => 'categories:parent:{id}',
        ],
        'brands' => [
            'active' => 'brands:active',
            'by_id' => 'brands:id:{id}',
        ],
        'users' => [
            'profile' => 'users:profile:{id}',
            'preferences' => 'users:preferences:{id}',
            'cart' => 'users:cart:{id}',
            'wishlist' => 'users:wishlist:{id}',
        ],
        'orders' => [
            'by_user' => 'orders:user:{id}',
            'by_status' => 'orders:status:{status}',
            'recent' => 'orders:recent:{id}',
        ],
        'analytics' => [
            'overview' => 'analytics:overview',
            'sales' => 'analytics:sales',
            'users' => 'analytics:users',
            'products' => 'analytics:products',
        ],
        'search' => [
            'suggestions' => 'search:suggestions:{query}',
            'filters' => 'search:filters',
            'results' => 'search:results:{query}:{filters}',
        ],
        'api' => [
            'rate_limit' => 'api:rate_limit:{user_id}',
            'response' => 'api:response:{endpoint}:{params}',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Health Check Configuration
    |--------------------------------------------------------------------------
    |
    | Configure cache health monitoring.
    |
    */

    'health_check' => [
        'enabled' => env('CACHE_HEALTH_CHECK_ENABLED', true),
        'interval' => env('CACHE_HEALTH_CHECK_INTERVAL', 300), // 5 minutes
        'test_key' => 'cache:health:test',
        'test_ttl' => 60, // 1 minute
    ],
];
