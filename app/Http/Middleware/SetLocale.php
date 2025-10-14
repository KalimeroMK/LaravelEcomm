<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locales = config('app.locales', []);
        
        // Priority order for locale detection:
        // 1. User's saved preference (if authenticated)
        // 2. Session locale
        // 3. Browser language
        // 4. Default locale
        
        $locale = null;
        
        // Check user's saved preference
        if (auth()->check() && auth()->user()->locale) {
            $locale = auth()->user()->locale;
        }
        
        // Check session
        if (!$locale && Session::has('locale')) {
            $locale = Session::get('locale');
        }
        
        // Check browser language
        if (!$locale) {
            $browserLocale = $request->getPreferredLanguage(array_keys($locales));
            if ($browserLocale) {
                $locale = $browserLocale;
            }
        }
        
        // Fallback to default
        if (!$locale || !array_key_exists($locale, $locales)) {
            $locale = config('app.locale', 'en');
        }
        
        // Set the locale
        App::setLocale($locale);
        
        // Store in session for next request
        Session::put('locale', $locale);
        
        return $next($request);
    }
}
