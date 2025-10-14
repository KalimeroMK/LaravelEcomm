<?php

declare(strict_types=1);

if (!function_exists('theme_asset')) {
    /**
     * Generate a URL for a theme asset.
     */
    function theme_asset(string $path, ?string $theme = null): string
    {
        if ($theme === null) {
            try {
                $setting = \Modules\Settings\Models\Setting::first();
                $theme = $setting->active_template ?? 'default';
            } catch (\Exception $e) {
                $theme = 'default';
            }
        }
        return asset("frontend/themes/{$theme}/{$path}");
    }
}

if (!function_exists('theme_view')) {
    /**
     * Get the theme-specific view path.
     */
    function theme_view(string $view): string
    {
        try {
            $setting = \Modules\Settings\Models\Setting::first();
            $activeTheme = $setting->active_template ?? 'default';
        } catch (\Exception $e) {
            $activeTheme = 'default';
        }
        return "front::themes.{$activeTheme}.{$view}";
    }
}
