<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Rate Limiting Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the rate limiting settings for different
    | endpoints and user types in your application.
    |
    */

    'limits' => [
        // API Rate Limits
        'api' => [
            'default' => [
                'max_attempts' => 100,
                'decay_minutes' => 1,
            ],
            'auth' => [
                'max_attempts' => 5,
                'decay_minutes' => 1,
            ],
            'search' => [
                'max_attempts' => 30,
                'decay_minutes' => 1,
            ],
            'upload' => [
                'max_attempts' => 10,
                'decay_minutes' => 1,
            ],
        ],

        // Web Rate Limits
        'web' => [
            'default' => [
                'max_attempts' => 60,
                'decay_minutes' => 1,
            ],
            'login' => [
                'max_attempts' => 5,
                'decay_minutes' => 15,
            ],
            'register' => [
                'max_attempts' => 3,
                'decay_minutes' => 60,
            ],
            'password_reset' => [
                'max_attempts' => 3,
                'decay_minutes' => 60,
            ],
        ],

        // Admin Rate Limits
        'admin' => [
            'default' => [
                'max_attempts' => 200,
                'decay_minutes' => 1,
            ],
            'analytics' => [
                'max_attempts' => 50,
                'decay_minutes' => 1,
            ],
            'export' => [
                'max_attempts' => 10,
                'decay_minutes' => 1,
            ],
        ],

        // User-specific limits
        'user_tiers' => [
            'guest' => [
                'max_attempts' => 30,
                'decay_minutes' => 1,
            ],
            'registered' => [
                'max_attempts' => 100,
                'decay_minutes' => 1,
            ],
            'premium' => [
                'max_attempts' => 200,
                'decay_minutes' => 1,
            ],
            'admin' => [
                'max_attempts' => 500,
                'decay_minutes' => 1,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting Keys
    |--------------------------------------------------------------------------
    |
    | Define custom rate limiting keys for different scenarios.
    |
    */

    'keys' => [
        'api_auth' => 'api:auth',
        'api_search' => 'api:search',
        'api_upload' => 'api:upload',
        'web_login' => 'web:login',
        'web_register' => 'web:register',
        'admin_analytics' => 'admin:analytics',
        'admin_export' => 'admin:export',
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting Exceptions
    |--------------------------------------------------------------------------
    |
    | Define IP addresses or user agents that should be exempt from rate limiting.
    |
    */

    'exceptions' => [
        'ips' => [
            '127.0.0.1',
            '::1',
            // Add trusted IPs here
        ],
        'user_agents' => [
            'Googlebot',
            'Bingbot',
            // Add trusted user agents here
        ],
    ],
];
