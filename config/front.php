<?php

declare(strict_types=1);

/**
 * Frontend Theme Configuration
 * 
 * Priority order for theme resolution:
 * 1. FRONT_ACTIVE_TEMPLATE env (for dev/testing)
 * 2. Database settings.active_template (for production)
 * 3. 'default' as final fallback
 */

return [
    /**
     * Active theme template.
     * Set to null to use database value from settings table.
     * Set to specific theme name to override database (useful for dev).
     */
    'active_template' => env('FRONT_ACTIVE_TEMPLATE', null),
    
    /**
     * Available themes cache time in seconds.
     * Set to 0 to disable caching.
     */
    'theme_cache_ttl' => env('FRONT_THEME_CACHE_TTL', 3600),
    
    /**
     * Auto-clear view cache on theme switch.
     * Recommended: true
     */
    'auto_clear_cache' => env('FRONT_AUTO_CLEAR_CACHE', true),
    
    /**
     * Theme assets configuration
     */
    'assets' => [
        // URL path to themes (relative to public)
        'path' => 'frontend/themes',
        
        // Default assets that should exist in every theme
        'required' => [
            'css/style.css',
            'js/main.js',
        ],
        
        // Fallback to default theme if asset not found in active theme
        'fallback_to_default' => true,
    ],
    
    /**
     * View configuration
     */
    'views' => [
        // View paths to check (in order)
        'paths' => [
            'themes.{theme}.pages.{view}',
            'themes.{theme}.{view}',
        ],
        
        // Always fallback to default theme if view not found
        'fallback_to_default' => true,
    ],
];
