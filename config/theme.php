<?php

return [
    // Default fallback theme
    'default_theme' => env('DEFAULT_THEME', 'default'),

    // List of available themes
    'themes' => [
        'default' => [
            'name' => 'Default Theme',
            'description' => 'The default theme with basic styles.',
            'assets_path' => 'theme/default',
            'settings' => [
                'logo' => 'theme/default/images/logo.png',
                'favicon' => 'theme/default/images/favicon.png',
                'primary_color' => '#007bff',
                'secondary_color' => '#6c757d',
            ],
        ],
        'ashion' => [
            'name' => 'Ashion Theme',
            'description' => 'The stylish Ashion theme.',
            'assets_path' => 'theme/ashion',
            'settings' => [
                'logo' => 'theme/ashion/img/logo.png',
                'favicon' => 'theme/ashion/img/favicon.png',
                'primary_color' => '#1e90ff',
                'secondary_color' => '#ff6347',
            ],
        ],
    ],

    // Active theme
    'active_theme' => env('ACTIVE_THEME', 'default'),
];
