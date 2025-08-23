<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class CacheService
{
    private const DEFAULT_TTL = 86400; // 24 hours

    private const SHORT_TTL = 3600;    // 1 hour

    private const LONG_TTL = 604800;   // 1 week

    /**
     * Get cached data with intelligent key generation
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
     * Store data in cache with custom TTL
     */
    public function put(string $key, mixed $value, ?int $ttl = null): bool
    {
        $ttl = $ttl ?? self::DEFAULT_TTL;
        $cacheKey = $this->generateKey($key);

        return Cache::store('redis')->put($cacheKey, $value, $ttl);
    }

    /**
     * Get data from cache
     */
    public function get(string $key): mixed
    {
        $cacheKey = $this->generateKey($key);

        return Cache::store('redis')->get($cacheKey);
    }

    /**
     * Check if key exists in cache
     */
    public function has(string $key): bool
    {
        $cacheKey = $this->generateKey($key);

        return Cache::store('redis')->has($cacheKey);
    }

    /**
     * Remove data from cache
     */
    public function forget(string $key): bool
    {
        $cacheKey = $this->generateKey($key);

        return Cache::store('redis')->forget($cacheKey);
    }

    /**
     * Clear all cache
     */
    public function flush(): bool
    {
        return Cache::store('redis')->clear();
    }

    /**
     * Clear cache by pattern
     */
    public function clearPattern(string $pattern): int
    {
        $keys = Redis::keys($pattern);
        $deleted = 0;

        foreach ($keys as $key) {
            if (Redis::del($key)) {
                $deleted++;
            }
        }

        return $deleted;
    }

    /**
     * Clear product-related cache
     */
    public function clearProductCache(?int $productId = null): void
    {
        if ($productId) {
            $this->clearPattern("*product*{$productId}*");
        } else {
            $this->clearPattern('*product*');
        }
    }

    /**
     * Clear category-related cache
     */
    public function clearCategoryCache(?int $categoryId = null): void
    {
        if ($categoryId) {
            $this->clearPattern("*category*{$categoryId}*");
        } else {
            $this->clearPattern('*category*');
        }
    }

    /**
     * Clear user-related cache
     */
    public function clearUserCache(?int $userId = null): void
    {
        if ($userId) {
            $this->clearPattern("*user*{$userId}*");
        } else {
            $this->clearPattern('*user*');
        }
    }

    /**
     * Clear order-related cache
     */
    public function clearOrderCache(?int $orderId = null): void
    {
        if ($orderId) {
            $this->clearPattern("*order*{$orderId}*");
        } else {
            $this->clearPattern('*order*');
        }
    }

    /**
     * Get cache statistics
     */
    public function getStats(): array
    {
        $info = Redis::info();

        return [
            'used_memory' => $info['used_memory_human'] ?? 'N/A',
            'connected_clients' => $info['connected_clients'] ?? 0,
            'total_commands_processed' => $info['total_commands_processed'] ?? 0,
            'keyspace_hits' => $info['keyspace_hits'] ?? 0,
            'keyspace_misses' => $info['keyspace_misses'] ?? 0,
            'hit_rate' => $this->calculateHitRate($info),
        ];
    }

    /**
     * Warm up cache for frequently accessed data
     */
    public function warmUp(): void
    {
        // Warm up product cache
        $this->warmUpProducts();

        // Warm up category cache
        $this->warmUpCategories();

        // Warm up settings cache
        $this->warmUpSettings();
    }

    /**
     * Generate cache key with prefix
     */
    private function generateKey(string $key): string
    {
        $prefix = config('cache.prefix', 'laravel_cache');

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

    /**
     * Warm up product cache
     */
    private function warmUpProducts(): void
    {
        // This would be implemented based on your product model
        // Example: cache featured products, latest products, etc.
    }

    /**
     * Warm up category cache
     */
    private function warmUpCategories(): void
    {
        // This would be implemented based on your category model
        // Example: cache category tree, active categories, etc.
    }

    /**
     * Warm up settings cache
     */
    private function warmUpSettings(): void
    {
        // This would be implemented based on your settings model
        // Example: cache site settings, configuration, etc.
    }
}
