<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class RateLimitMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $type = 'default'): Response
    {
        $key = $this->resolveRequestSignature($request, $type);

        if ($this->isBlocked($key)) {
            return response()->json([
                'error' => 'Too many requests. Please try again later.',
                'retry_after' => $this->getBlockTimeRemaining($key),
            ], 429);
        }

        if (RateLimiter::tooManyAttempts($key, $this->getMaxAttempts($type))) {
            $this->blockRequest($key);

            return response()->json([
                'error' => 'Too many requests. Your IP has been temporarily blocked.',
                'retry_after' => $this->getBlockTimeRemaining($key),
            ], 429);
        }

        RateLimiter::hit($key, $this->getDecayMinutes($type) * 60);

        $response = $next($request);

        return $response->header('X-RateLimit-Limit', $this->getMaxAttempts($type))
            ->header('X-RateLimit-Remaining', RateLimiter::remaining($key, $this->getMaxAttempts($type)))
            ->header('X-RateLimit-Reset', time() + ($this->getDecayMinutes($type) * 60));
    }

    /**
     * Resolve the request signature for rate limiting
     */
    private function resolveRequestSignature(Request $request, string $type): string
    {
        $identifier = $request->ip();

        // Add user ID if authenticated for more granular rate limiting
        if ($request->user()) {
            $identifier .= ':'.$request->user()->id;
        }

        // Add route-specific identifier
        $routeIdentifier = $request->route() ? $request->route()->getName() : $request->path();

        return "rate_limit:{$type}:{$identifier}:{$routeIdentifier}";
    }

    /**
     * Get maximum attempts based on request type
     */
    private function getMaxAttempts(string $type): int
    {
        return match ($type) {
            'strict' => 30,      // Very strict - 30 requests per time window
            'api' => 100,        // API endpoints - 100 requests per time window
            'auth' => 5,         // Authentication - 5 attempts per time window
            'search' => 200,     // Search endpoints - 200 requests per time window
            'upload' => 10,      // File uploads - 10 requests per time window
            default => 60,       // Default - 60 requests per time window
        };
    }

    /**
     * Get decay minutes based on request type
     */
    private function getDecayMinutes(string $type): int
    {
        return match ($type) {
            'strict' => 1,       // 1 minute window
            'api' => 1,          // 1 minute window
            'auth' => 15,        // 15 minutes window
            'search' => 1,       // 1 minute window
            'upload' => 5,       // 5 minutes window
            default => 1,        // 1 minute window
        };
    }

    /**
     * Check if the request is blocked
     */
    private function isBlocked(string $key): bool
    {
        return Cache::has("blocked:{$key}");
    }

    /**
     * Block the request for a period of time
     */
    private function blockRequest(string $key): void
    {
        $blockTime = $this->getBlockTime();
        Cache::put("blocked:{$key}", true, $blockTime);

        // Log the blocking for security monitoring
        \Illuminate\Support\Facades\Log::warning('Rate limit exceeded and IP blocked', [
            'key' => $key,
            'block_time' => $blockTime,
            'timestamp' => now(),
        ]);
    }

    /**
     * Get block time in seconds
     */
    private function getBlockTime(): int
    {
        return 300; // 5 minutes
    }

    /**
     * Get remaining block time
     */
    private function getBlockTimeRemaining(string $key): int
    {
        $ttl = Cache::get("blocked:{$key}");

        return $ttl ? max(0, $ttl) : 0;
    }
}
