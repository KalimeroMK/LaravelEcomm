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
        'theme1' => [
            'name' => 'Modern Theme',
            'description' => 'A modern, sleek design.',
            'assets_path' => 'theme/theme1',
            'settings' => [
                'logo' => 'theme/theme1/images/logo.png',
                'favicon' => 'theme/theme1/images/favicon.png',
                'primary_color' => '#1e90ff',
                'secondary_color' => '#ff6347',
            ],
        ],
        'theme2' => [
            'name' => 'Dark Theme',
            'description' => 'A theme with dark mode styling.',
            'assets_path' => 'theme/theme2',
            'settings' => [
                'logo' => 'theme/theme2/images/logo.png',
                'favicon' => 'theme/theme2/images/favicon.png',
                'primary_color' => '#343a40',
                'secondary_color' => '#adb5bd',
            ],
        ],
    ],

    // Active theme
    'active_theme' => env('ACTIVE_THEME', 'default'),
];
