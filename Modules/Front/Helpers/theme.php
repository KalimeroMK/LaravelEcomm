<?php

declare(strict_types=1);

if (! function_exists('active_theme')) {
    /**
     * Get the currently active theme.
     * Priority: env > database > default
     */
    function active_theme(): string
    {
        static $cachedTheme = null;
        
        // Return cached theme if available
        if ($cachedTheme !== null) {
            return $cachedTheme;
        }
        
        try {
            // Priority 1: Environment/Config override (for dev/testing)
            $envTheme = config('front.active_template');
            if ($envTheme && is_string($envTheme)) {
                // Verify theme exists
                $themePath = module_path('Front', "Resources/views/themes/{$envTheme}");
                if (is_dir($themePath)) {
                    $cachedTheme = $envTheme;
                    return $cachedTheme;
                }
            }
            
            // Priority 2: Database (for production)
            if (! app()->runningInConsole() || Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $setting = app('settings');
                if ($setting instanceof \Modules\Settings\Models\Setting) {
                    $dbTheme = $setting->active_template ?? 'default';
                    $themePath = module_path('Front', "Resources/views/themes/{$dbTheme}");
                    if (is_dir($themePath)) {
                        $cachedTheme = $dbTheme;
                        return $cachedTheme;
                    }
                }
            }
        } catch (\Exception $e) {
            // Silently fall through to default
        }
        
        // Priority 3: Default fallback
        $cachedTheme = 'default';
        return $cachedTheme;
    }
}

if (! function_exists('clear_theme_cache')) {
    /**
     * Clear theme-related caches.
     */
    function clear_theme_cache(): void
    {
        // Clear view cache
        Illuminate\Support\Facades\Artisan::call('view:clear');
        
        // Clear application cache
        Illuminate\Support\Facades\Artisan::call('cache:clear');
        
        // Clear static theme cache
        \Illuminate\Support\Facades\Cache::forget('active_theme');
    }
}

if (! function_exists('theme_asset')) {
    /**
     * Generate a URL for a theme asset.
     */
    function theme_asset(string $path, ?string $theme = null): string
    {
        $theme = $theme ?? active_theme();
        $assetPath = "frontend/themes/{$theme}/{$path}";
        
        // Check if asset exists, fallback to default theme if configured
        if (config('front.assets.fallback_to_default', true) && $theme !== 'default') {
            $fullPath = public_path($assetPath);
            if (! file_exists($fullPath)) {
                $fallbackPath = "frontend/themes/default/{$path}";
                if (file_exists(public_path($fallbackPath))) {
                    return asset($fallbackPath);
                }
            }
        }
        
        return asset($assetPath);
    }
}

if (! function_exists('theme_view')) {
    /**
     * Get the theme-specific view path with fallback to default theme.
     */
    function theme_view(string $view): string
    {
        $activeTheme = active_theme();
        
        // Try different view path formats (pages subdirectory first)
        $viewFormats = [
            "front::themes.{$activeTheme}.pages.{$view}",
            "front::themes.{$activeTheme}.{$view}",
        ];

        // Check if view exists in active theme
        foreach ($viewFormats as $themeView) {
            if (Illuminate\Support\Facades\View::exists($themeView)) {
                return $themeView;
            }
        }

        // Fallback to default theme if configured and theme is not default
        if (config('front.views.fallback_to_default', true) && $activeTheme !== 'default') {
            $defaultFormats = [
                "front::themes.default.pages.{$view}",
                "front::themes.default.{$view}",
            ];

            foreach ($defaultFormats as $defaultView) {
                if (Illuminate\Support\Facades\View::exists($defaultView)) {
                    return $defaultView;
                }
            }
        }

        // Return the first format (will show error if view doesn't exist)
        return $viewFormats[0];
    }
}

if (! function_exists('get_available_themes')) {
    /**
     * Get list of available themes from filesystem.
     */
    function get_available_themes(): array
    {
        $themesPath = module_path('Front', 'Resources/views/themes');

        if (! is_dir($themesPath)) {
            return ['default'];
        }

        $themes = [];
        $directories = scandir($themesPath);

        foreach ($directories as $dir) {
            if ($dir !== '.' && $dir !== '..' && is_dir($themesPath.'/'.$dir)) {
                $themes[] = $dir;
            }
        }

        // Ensure default theme is always first
        if (in_array('default', $themes, true)) {
            $themes = array_diff($themes, ['default']);
            array_unshift($themes, 'default');
        }

        return array_values($themes);
    }
}
