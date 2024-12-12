<?php

namespace Modules\Front\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\View;

class ThemeMiddleware
{
    public function handle($request, Closure $next)
    {
        // Get the active theme
        $activeTheme = config('theme.active_theme', config('theme.default_theme'));

        // Get the active theme's asset path
        $themeConfig = config("theme.themes.$activeTheme", []);
        $assetsPath = $themeConfig['assets_path'] ?? 'theme/default';
        // Share active theme and its assets path with views
        View::share('activeTheme', $activeTheme);
        View::share('themeAssetsPath', $assetsPath);

        return $next($request);
    }
}
