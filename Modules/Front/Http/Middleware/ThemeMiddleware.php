<?php

namespace Modules\Front\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\View;

class ThemeMiddleware
{
    public function handle($request, Closure $next)
    {
        $theme = config('admin.default_theme');
        View::share('theme', $theme);
        return $next($request);
    }
}
