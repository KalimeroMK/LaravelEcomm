<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Language\Models\Language;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to handle locale for API requests
 * 
 * Checks X-Locale header or falls back to default
 */
class ApiLocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->getLocaleFromRequest($request);

        // Validate and set locale
        if ($locale && Language::isValidCode($locale)) {
            app()->setLocale($locale);
        } else {
            // Use default locale
            app()->setLocale(Language::getDefaultCode());
        }

        // Add locale to response headers
        $response = $next($request);
        $response->header('Content-Language', app()->getLocale());

        return $response;
    }

    /**
     * Get locale from request
     */
    private function getLocaleFromRequest(Request $request): ?string
    {
        // Check X-Locale header first
        if ($request->hasHeader('X-Locale')) {
            return $request->header('X-Locale');
        }

        // Check Accept-Language header
        if ($request->hasHeader('Accept-Language')) {
            $acceptLanguage = $request->header('Accept-Language');
            $locale = substr($acceptLanguage, 0, 2);
            
            if (Language::isValidCode($locale)) {
                return $locale;
            }
        }

        // Check query parameter
        if ($request->has('locale')) {
            return $request->get('locale');
        }

        return null;
    }
}
