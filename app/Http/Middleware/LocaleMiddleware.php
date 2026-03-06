<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Modules\Language\Models\Language;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to handle locale from URL prefix
 * 
 * URLs: /en/products, /mk/products, /de/products
 * Falls back to default language if locale is invalid
 */
class LocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->getLocaleFromRequest($request);

        // Validate locale
        if ($this->isValidLocale($locale)) {
            app()->setLocale($locale);
            session()->put('locale', $locale);
            // Set default locale parameter for URL generation
            URL::defaults(['locale' => $locale]);
        } else {
            // Redirect to default locale
            $defaultLocale = Language::getDefaultCode();
            
            // Only redirect GET requests to avoid breaking form submissions
            if ($request->isMethod('GET')) {
                $newUrl = '/' . $defaultLocale . $request->getPathInfo();
                if ($request->getQueryString()) {
                    $newUrl .= '?' . $request->getQueryString();
                }
                
                return redirect($newUrl, 301);
            }
            
            // For non-GET requests, just set default locale
            app()->setLocale($defaultLocale);
        }

        return $next($request);
    }

    /**
     * Get locale from request
     */
    private function getLocaleFromRequest(Request $request): ?string
    {
        // First check URL prefix: /en/, /mk/, etc.
        $path = $request->path();
        $segments = explode('/', $path);
        
        if (count($segments) > 0 && strlen($segments[0]) === 2) {
            return $segments[0];
        }

        // Then check session
        if (session()->has('locale')) {
            return session('locale');
        }

        // Finally check header (for API)
        if ($request->hasHeader('X-Locale')) {
            return $request->header('X-Locale');
        }

        return null;
    }

    /**
     * Check if locale is valid and active
     */
    private function isValidLocale(?string $locale): bool
    {
        if ($locale === null || strlen($locale) !== 2) {
            return false;
        }

        return Language::isValidCode($locale);
    }
}
