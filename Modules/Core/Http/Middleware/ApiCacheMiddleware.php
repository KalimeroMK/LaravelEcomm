<?php

declare(strict_types=1);

namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Core\Services\CacheService;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class ApiCacheMiddleware
{
    private const CACHEABLE_METHODS = ['GET'];

    private const DEFAULT_TTL = 1800; // 30 minutes

    private const LONG_TTL = 3600; // 1 hour

    private const SHORT_TTL = 300; // 5 minutes

    public function __construct(private CacheService $cacheService) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): BaseResponse
    {
        // Only cache GET requests
        if (! in_array($request->method(), self::CACHEABLE_METHODS)) {
            return $next($request);
        }

        // Skip caching for authenticated requests with sensitive data
        if ($this->shouldSkipCache($request)) {
            return $next($request);
        }

        $cacheKey = $this->generateCacheKey($request);
        $ttl = $this->getCacheTtl($request);

        // Try to get from cache
        $cachedResponse = $this->cacheService->rememberApiResponse(
            $request->path(),
            $request->query(),
            function () use ($next, $request) {
                $response = $next($request);

                // Only cache successful responses
                if ($response->getStatusCode() === 200) {
                    return [
                        'content' => $response->getContent(),
                        'headers' => $response->headers->all(),
                        'status' => $response->getStatusCode(),
                    ];
                }

                return null;
            },
            $ttl
        );

        if ($cachedResponse) {
            $response = new Response(
                $cachedResponse['content'],
                $cachedResponse['status'],
                $cachedResponse['headers']
            );

            // Add cache headers
            $response->headers->set('X-Cache', 'HIT');
            $response->headers->set('X-Cache-TTL', (string) $ttl);

            return $response;
        }

        $response = $next($request);

        // Add cache miss header
        $response->headers->set('X-Cache', 'MISS');

        return $response;
    }

    /**
     * Determine if request should skip caching
     */
    private function shouldSkipCache(Request $request): bool
    {
        // Skip for authenticated requests with sensitive data
        if ($request->user() && $this->hasSensitiveData($request)) {
            return true;
        }

        // Skip for admin routes
        if (str_starts_with($request->path(), 'admin/')) {
            return true;
        }

        // Skip for user-specific routes
        $userSpecificRoutes = [
            'api/v1/cart',
            'api/v1/wishlist',
            'api/v1/orders',
            'api/v1/profile',
        ];

        foreach ($userSpecificRoutes as $route) {
            if (str_starts_with($request->path(), $route)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if request contains sensitive data
     */
    private function hasSensitiveData(Request $request): bool
    {
        $sensitiveParams = ['user_id', 'token', 'password', 'email'];

        foreach ($sensitiveParams as $param) {
            if ($request->has($param)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate cache key for request
     */
    private function generateCacheKey(Request $request): string
    {
        $params = $request->query();
        ksort($params);

        return 'api:'.$request->path().':'.md5(serialize($params));
    }

    /**
     * Get cache TTL based on endpoint
     */
    private function getCacheTtl(Request $request): int
    {
        $path = $request->path();

        // Long cache for static content
        if (str_contains($path, 'categories') || str_contains($path, 'brands')) {
            return self::LONG_TTL;
        }

        // Short cache for dynamic content
        if (str_contains($path, 'products') || str_contains($path, 'search')) {
            return self::SHORT_TTL;
        }

        // Default cache
        return self::DEFAULT_TTL;
    }
}
