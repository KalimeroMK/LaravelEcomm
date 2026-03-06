<?php

declare(strict_types=1);

namespace Modules\GeoLocalization\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\GeoLocalization\Services\GeoIpService;
use Symfony\Component\HttpFoundation\Response;

class GeoIpMiddleware
{
    public function __construct(
        private readonly GeoIpService $geoIpService,
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip if disabled
        if (! config('geolocalization.enabled', true)) {
            return $next($request);
        }

        // Skip for bots/crawlers
        if ($this->isBot($request)) {
            return $next($request);
        }

        // Get or detect location
        $location = $this->getLocation($request);

        // Store in request attributes for later use
        $request->attributes->set('location', $location);
        $request->attributes->set('country_code', $location->countryCode);
        $request->attributes->set('currency', $location->currency);
        $request->attributes->set('timezone', $location->timezone);

        // Apply detected settings
        $this->applyLocationSettings($request, $location);

        // Add location headers to response
        $response = $next($request);
        $this->addLocationHeaders($response, $location);

        return $response;
    }

    /**
     * Get location from request (session, cookie, or detect)
     */
    private function getLocation(Request $request): \Modules\GeoLocalization\DTOs\LocationDTO
    {
        // Check for manual override in query string
        if ($request->has('country')) {
            $countryCode = strtoupper($request->get('country'));
            session()->put('country_code', $countryCode);
        }

        // Check session
        if (session()->has('country_code')) {
            $cachedLocation = session('location');
            if ($cachedLocation) {
                return $cachedLocation;
            }
        }

        // Detect from IP
        $ip = $this->geoIpService->getClientIp();
        $location = $this->geoIpService->locate($ip);

        // Store in session
        session()->put('location', $location);
        session()->put('country_code', $location->countryCode);
        session()->put('currency', $location->currency);
        session()->put('timezone', $location->timezone);

        return $location;
    }

    /**
     * Apply location-based settings
     */
    private function applyLocationSettings(Request $request, \Modules\GeoLocalization\DTOs\LocationDTO $location): void
    {
        // Set timezone for the request
        if ($location->timezone) {
            // Don't change app timezone, just store for reference
            $request->attributes->set('detected_timezone', $location->timezone);
        }

        // Set currency if not already set
        if (! session()->has('currency')) {
            session()->put('currency', $location->currency ?? config('app.default_currency', 'USD'));
        }

        // Set locale based on country if auto-detection enabled
        if (config('geolocalization.auto_locale', true) && $location->countryCode) {
            $locale = $this->getLocaleByCountry($location->countryCode);
            if ($locale && ! session()->has('locale')) {
                session()->put('locale', $locale);
                app()->setLocale($locale);
            }
        }
    }

    /**
     * Add location headers to response
     */
    private function addLocationHeaders(Response $response, \Modules\GeoLocalization\DTOs\LocationDTO $location): void
    {
        // Only add headers if enabled (for debugging/API purposes)
        if (! config('geolocalization.expose_headers', false)) {
            return;
        }

        $response->headers->set('X-Country-Code', $location->countryCode ?? 'unknown');
        $response->headers->set('X-Currency', $location->currency ?? 'unknown');
        $response->headers->set('X-Timezone', $location->timezone ?? 'unknown');
    }

    /**
     * Check if request is from a bot/crawler
     */
    private function isBot(Request $request): bool
    {
        $userAgent = strtolower($request->userAgent() ?? '');
        
        $bots = [
            'bot', 'crawl', 'spider', 'slurp', 'baidu', 'bing', 'google',
            'yahoo', 'yandex', 'facebook', 'twitter', 'linkedin',
        ];

        foreach ($bots as $bot) {
            if (str_contains($userAgent, $bot)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get locale by country code
     */
    private function getLocaleByCountry(string $countryCode): ?string
    {
        $map = [
            'US' => 'en',
            'GB' => 'en',
            'CA' => 'en',
            'AU' => 'en',
            'DE' => 'de',
            'AT' => 'de',
            'CH' => 'de',
            'FR' => 'fr',
            'ES' => 'es',
            'IT' => 'it',
            'PT' => 'pt',
            'NL' => 'nl',
            'BE' => 'nl',
            'PL' => 'pl',
            'RU' => 'ru',
            'JP' => 'ja',
            'CN' => 'zh',
            'MK' => 'mk',
            'AL' => 'sq',
            'RS' => 'sr',
            'HR' => 'hr',
            'BA' => 'bs',
            'SI' => 'sl',
            'BG' => 'bg',
            'RO' => 'ro',
            'GR' => 'el',
            'TR' => 'tr',
        ];

        return $map[$countryCode] ?? null;
    }
}
