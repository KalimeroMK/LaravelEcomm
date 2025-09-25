<?php

declare(strict_types=1);

namespace Modules\Front\Services;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class FrontCacheService
{
    private const DEFAULT_TTL = 3600;      // 1 hour

    private const SHORT_TTL = 900;         // 15 minutes

    private const LONG_TTL = 86400;        // 24 hours

    private const SEARCH_TTL = 1800;       // 30 minutes

    /**
     * Get cached data for front-end content
     */
    public function remember(string $key, callable $callback, ?int $ttl = null): mixed
    {
        $ttl = $ttl ?? self::DEFAULT_TTL;
        $cacheKey = $this->generateKey($key);

        return Cache::store('redis')->remember($cacheKey, $ttl, $callback);
    }

    /**
     * Get cached data for short-lived content
     */
    public function rememberShort(string $key, callable $callback): mixed
    {
        return $this->remember($key, $callback, self::SHORT_TTL);
    }

    /**
     * Get cached data for long-lived content
     */
    public function rememberLong(string $key, callable $callback): mixed
    {
        return $this->remember($key, $callback, self::LONG_TTL);
    }

    /**
     * Get cached data for search results
     */
    public function rememberSearch(string $key, callable $callback): mixed
    {
        return $this->remember($key, $callback, self::SEARCH_TTL);
    }

    /**
     * Clear front-end specific cache
     */
    public function clearFrontCache(): int
    {
        return $this->clearPattern('front_*');
    }

    /**
     * Clear product-related cache
     */
    public function clearProductCache(): int
    {
        return $this->clearPattern('*product*');
    }

    /**
     * Clear category-related cache
     */
    public function clearCategoryCache(): int
    {
        return $this->clearPattern('*category*');
    }

    /**
     * Clear brand-related cache
     */
    public function clearBrandCache(): int
    {
        return $this->clearPattern('*brand*');
    }

    /**
     * Clear search cache
     */
    public function clearSearchCache(): int
    {
        return $this->clearPattern('*search*');
    }

    /**
     * Clear all front-end cache
     */
    public function clearAll(): int
    {
        $patterns = [
            'front_*',
            '*product*',
            '*category*',
            '*brand*',
            '*search*',
            'featured_*',
            'latest_*',
            'recent_*',
            'hot_*',
            'blog_*',
            'banner_*',
        ];

        $totalCleared = 0;
        foreach ($patterns as $pattern) {
            $totalCleared += $this->clearPattern($pattern);
        }

        return $totalCleared;
    }

    /**
     * Get cache statistics for front-end
     */
    public function getStats(): array
    {
        try {
            $info = Redis::info();

            return [
                'used_memory' => $info['used_memory_human'] ?? 'N/A',
                'connected_clients' => $info['connected_clients'] ?? 0,
                'keyspace_hits' => $info['keyspace_hits'] ?? 0,
                'keyspace_misses' => $info['keyspace_misses'] ?? 0,
                'hit_rate' => $this->calculateHitRate($info),
                'total_keys' => Redis::dbsize(),
            ];
        } catch (Exception $e) {
            return [
                'used_memory' => 'N/A',
                'connected_clients' => 0,
                'keyspace_hits' => 0,
                'keyspace_misses' => 0,
                'hit_rate' => 0.0,
                'total_keys' => 0,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Warm up frequently accessed front-end data
     */
    public function warmUp(): void
    {
        // This method can be called to pre-load frequently accessed data
        // Implementation would depend on your specific needs
    }

    /**
     * Clear cache by pattern
     */
    private function clearPattern(string $pattern): int
    {
        $keys = Redis::keys($pattern);
        $deleted = 0;

        foreach ($keys as $key) {
            if (Cache::store('redis')->forget($key)) {
                $deleted++;
            }
        }

        return $deleted;
    }

    /**
     * Generate cache key with prefix
     */
    private function generateKey(string $key): string
    {
        $prefix = 'front_cache';

        return Str::slug($prefix.'_'.$key);
    }

    /**
     * Calculate cache hit rate
     */
    private function calculateHitRate(array $info): float
    {
        $hits = $info['keyspace_hits'] ?? 0;
        $misses = $info['keyspace_misses'] ?? 0;
        $total = $hits + $misses;

        if ($total === 0) {
            return 0.0;
        }

        return round(($hits / $total) * 100, 2);
    }
}
