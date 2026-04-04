<?php

declare(strict_types=1);

if (! function_exists('active_theme')) {
    /**
     * Get the currently active theme.
     * Priority: env > database > default
     *
     * Uses Cache::remember() instead of a static variable so that
     * long-running worker processes (FrankenPHP / Octane) and
     * multi-tenant setups (each tenant has its own cache prefix)
     * always resolve the correct theme per request context.
     */
    function active_theme(): string
    {
        try {
            // Priority 1: Environment/Config override (for dev/testing)
            $envTheme = config('front.active_template');
            if ($envTheme && is_string($envTheme)) {
                $themePath = module_path('Front', "Resources/views/themes/{$envTheme}");
                if (is_dir($themePath)) {
                    return $envTheme;
                }
            }

            // Priority 2: Database — cached for 60 s so admin theme-switches
            // take effect within a minute without hammering the DB on every request.
            return \Illuminate\Support\Facades\Cache::remember('active_theme', 60, function () {
                if (! app()->runningInConsole() || \Illuminate\Support\Facades\Schema::hasTable('settings')) {
                    $setting = app('settings');
                    if ($setting instanceof \Modules\Settings\Models\Setting) {
                        $dbTheme = $setting->active_template ?? 'default';
                        $themePath = module_path('Front', "Resources/views/themes/{$dbTheme}");
                        if (is_dir($themePath)) {
                            return $dbTheme;
                        }
                    }
                }

                return 'default';
            });
        } catch (\Exception $e) {
            return 'default';
        }
    }
}

if (! function_exists('clear_theme_cache')) {
    /**
     * Clear theme-related caches.
     * Uses direct File/Cache operations instead of Artisan::call()
     * to avoid the overhead of bootstrapping the full Artisan kernel
     * inside a web request.
     */
    function clear_theme_cache(): void
    {
        // Clear compiled Blade views
        $compiledPath = config('view.compiled');
        if ($compiledPath && is_dir($compiledPath)) {
            \Illuminate\Support\Facades\File::cleanDirectory($compiledPath);
        }

        // Clear only theme-related cache keys, not the whole cache store
        \Illuminate\Support\Facades\Cache::forget('active_theme');

        // Clear product display caches that depend on the active theme
        foreach (['featured_products', 'latest_products', 'hot_products', 'all_products', 'active_banners_with_categories'] as $key) {
            \Illuminate\Support\Facades\Cache::forget($key);
        }
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
