<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Blaze Enable/Disable
    |--------------------------------------------------------------------------
    | Master switch for Blaze optimization. When disabled, standard Blade
    | rendering is used.
    */
    'enabled' => env('BLAZE_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Debug Mode
    |--------------------------------------------------------------------------
    | Enables the debug overlay and profiler. Useful for measuring
    | performance improvements.
    */
    'debug' => env('BLAZE_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Throw Exceptions
    |--------------------------------------------------------------------------
    | When enabled, Blaze will throw exceptions instead of silently
    | falling back to standard Blade rendering.
    */
    'throw_exceptions' => env('BLAZE_THROW', false),

    /*
    |--------------------------------------------------------------------------
    | Theme Configuration
    |--------------------------------------------------------------------------
    | Configure optimization strategies per theme. Each theme can have
    | different optimization levels based on its complexity.
    |
    | Strategies:
    | - compile: Function compiler (91-97% faster) - SAFE for all components
    | - memo: Runtime memoization (caches repeated renders) - For icons/avatars
    | - fold: Compile-time folding (static HTML) - DANGEROUS, use carefully
    |
    | WARNING: Only use 'fold' for components without dynamic logic!
    */
    'themes' => [
        // Default theme - balanced performance
        'default' => [
            'enabled' => true,
            'strategy' => [
                'compile' => true,
                'memo' => true,
                'fold' => false,
            ],
            // Component-specific overrides
            'components' => [
                'compile' => ['*'], // Compile all by default
                'memo' => ['icon*', 'avatar*', 'badge*', 'button*'],
                'fold' => [], // No folding by default
                'exclude' => ['navigation', 'menu', 'cart'],
            ],
        ],

        // Modern theme - more aggressive optimization
        'modern' => [
            'enabled' => true,
            'strategy' => [
                'compile' => true,
                'memo' => true,
                'fold' => false,
            ],
            'components' => [
                'compile' => ['*'],
                'memo' => ['icon*', 'avatar*', 'badge*', 'button*', 'card*'],
                'fold' => ['spinner', 'divider', 'separator'],
                'exclude' => [],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Shared Components
    |--------------------------------------------------------------------------
    | Components that are shared across all themes and should always
    | be optimized regardless of active theme.
    */
    'shared_components' => [
        'enabled' => true,
        'path' => base_path('Modules/Front/Resources/views/components'),
        'strategy' => [
            'compile' => true,
            'memo' => false,
            'fold' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Global Exclusions
    |--------------------------------------------------------------------------
    | Views/directories that should NEVER be Blaze-optimized.
    | These typically use dynamic data that Blaze cannot handle.
    */
    'exclude' => [
        'errors/*',
        'emails/*',
        'vendor/*',
        'layouts/master', // Usually contains dynamic sections
        '*/checkout*', // Dynamic checkout pages
        '*/cart*', // Dynamic cart content
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Warming
    |--------------------------------------------------------------------------
    | Configuration for pre-compiling views during deployment.
    */
    'cache_warming' => [
        'enabled' => env('BLAZE_CACHE_WARM', true),
        'themes' => ['default', 'modern'],
        'concurrency' => 4,
    ],

    /*
    |--------------------------------------------------------------------------
    | View Composer Support
    |--------------------------------------------------------------------------
    | Your fork supports View::composer() - enable it here.
    */
    'view_composer_support' => true,

    /*
    |--------------------------------------------------------------------------
    | View Share Support
    |--------------------------------------------------------------------------
    | Your fork supports View::share() - enable it here.
    */
    'view_share_support' => true,
];
