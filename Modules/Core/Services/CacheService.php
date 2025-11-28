<?php

declare(strict_types=1);

namespace Modules\Core\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class CacheService
{
    private const DEFAULT_TTL = 3600; // 1 hour

    private const API_CACHE_PREFIX = 'api_cache:';

    private const QUERY_CACHE_PREFIX = 'query_cache:';

    /**
     * Cache API response with automatic key generation
     */
    public function rememberApiResponse(string $endpoint, array $params, callable $callback, int $ttl = self::DEFAULT_TTL): mixed
    {
        $key = $this->generateApiCacheKey($endpoint, $params);

        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Cache database query results
     */
    public function rememberQuery(string $model, string $method, array $params, callable $callback, int $ttl = self::DEFAULT_TTL): mixed
    {
        $key = $this->generateQueryCacheKey($model, $method, $params);

        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Cache with tags for easy invalidation
     */
    public function rememberWithTags(array $tags, string $key, callable $callback, int $ttl = self::DEFAULT_TTL): mixed
    {
        return Cache::tags($tags)->remember($key, $ttl, $callback);
    }

    /**
     * Invalidate cache by tags
     */
    public function invalidateByTags(array $tags): void
    {
        Cache::tags($tags)->flush();
    }

    /**
     * Invalidate API cache by pattern
     */
    public function invalidateApiCache(string $pattern): void
    {
        $keys = Redis::keys(self::API_CACHE_PREFIX.$pattern.'*');
        if (! empty($keys)) {
            Redis::del($keys);
        }
    }

    /**
     * Invalidate model cache
     */
    public function invalidateModelCache(string $model): void
    {
        $this->invalidateByTags([$model]);
        $this->invalidateApiCache($model);
    }

    /**
     * Get cache statistics
     */
    public function getStats(): array
    {
        $info = Redis::info();

        return [
            'memory_used' => $info['used_memory_human'] ?? 'N/A',
            'memory_peak' => $info['used_memory_peak_human'] ?? 'N/A',
            'hit_rate' => $this->calculateHitRate(),
            'total_keys' => Redis::dbsize(),
        ];
    }

    /**
     * Clear all cache
     */
    public function clearAll(): void
    {
        Cache::flush();
    }

    /**
     * Generate API cache key
     */
    private function generateApiCacheKey(string $endpoint, array $params): string
    {
        $sortedParams = $params;
        ksort($sortedParams);

        return self::API_CACHE_PREFIX.$endpoint.':'.md5(serialize($sortedParams));
    }

    /**
     * Generate query cache key
     */
    private function generateQueryCacheKey(string $model, string $method, array $params): string
    {
        $sortedParams = $params;
        ksort($sortedParams);

        return self::QUERY_CACHE_PREFIX.$model.':'.$method.':'.md5(serialize($sortedParams));
    }

    /**
     * Calculate cache hit rate
     */
    private function calculateHitRate(): float
    {
        $info = Redis::info();
        $hits = $info['keyspace_hits'] ?? 0;
        $misses = $info['keyspace_misses'] ?? 0;

        if ($hits + $misses === 0) {
            return 0.0;
        }

        return round(($hits / ($hits + $misses)) * 100, 2);
    }
}
