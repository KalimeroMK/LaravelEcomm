<?php

declare(strict_types=1);

if (! function_exists('theme_asset')) {
    /**
     * Generate a URL for a theme asset.
     */
    function theme_asset(string $path, ?string $theme = null): string
    {
        if ($theme === null) {
            try {
                $setting = Modules\Settings\Models\Setting::first();
                $theme = $setting->active_template ?? 'default';
            } catch (Exception $e) {
                $theme = 'default';
            }
        }

        return asset("frontend/themes/{$theme}/{$path}");
    }
}

if (! function_exists('theme_view')) {
    /**
     * Get the theme-specific view path with fallback to default theme.
     */
    function theme_view(string $view): string
    {
        try {
            $setting = Modules\Settings\Models\Setting::first();
            $activeTheme = $setting->active_template ?? 'default';
        } catch (Exception $e) {
            $activeTheme = 'default';
        }

        // Try different view path formats
        $viewFormats = [
            "front::themes.{$activeTheme}.{$view}",
            "front::themes.{$activeTheme}.pages.{$view}",
        ];

        // Check if view exists in active theme
        foreach ($viewFormats as $themeView) {
            if (Illuminate\Support\Facades\View::exists($themeView)) {
                return $themeView;
            }
        }

        // Fallback to default theme
        if ($activeTheme !== 'default') {
            $defaultFormats = [
                "front::themes.default.{$view}",
                "front::themes.default.pages.{$view}",
            ];

            foreach ($defaultFormats as $defaultView) {
                if (Illuminate\Support\Facades\View::exists($defaultView)) {
                    return $defaultView;
                }
            }
        }

        // Return the first format as default (will show error if view doesn't exist)
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
