<?php

declare(strict_types=1);

namespace Modules\Core\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Modules\Core\Services\CacheService;

trait Cacheable
{
    private static ?CacheService $cacheService = null;

    /**
     * Get cache service instance
     */
    private static function getCacheService(): CacheService
    {
        if (self::$cacheService === null) {
            self::$cacheService = app(CacheService::class);
        }

        return self::$cacheService;
    }

    /**
     * Cache model query results
     */
    public static function remember(string $key, callable $callback, int $ttl = 3600): mixed
    {
        return self::getCacheService()->rememberQuery(
            static::class,
            'remember',
            ['key' => $key],
            $callback,
            $ttl
        );
    }

    /**
     * Cache paginated results
     */
    public static function rememberPaginated(string $key, callable $callback, int $ttl = 1800): mixed
    {
        return self::getCacheService()->rememberQuery(
            static::class,
            'paginated',
            ['key' => $key],
            $callback,
            $ttl
        );
    }

    /**
     * Cache with tags for easy invalidation
     */
    public static function rememberWithTags(array $tags, string $key, callable $callback, int $ttl = 3600): mixed
    {
        return self::getCacheService()->rememberWithTags($tags, $key, $callback, $ttl);
    }

    /**
     * Invalidate model cache
     */
    public static function invalidateCache(): void
    {
        self::getCacheService()->invalidateModelCache(static::class);
    }

    /**
     * Boot cacheable trait
     */
    protected static function bootCacheable(): void
    {
        // Clear cache when model is created
        static::created(function (Model $model): void {
            static::invalidateCache();
        });

        // Clear cache when model is updated
        static::updated(function (Model $model): void {
            static::invalidateCache();
        });

        // Clear cache when model is deleted
        static::deleted(function (Model $model): void {
            static::invalidateCache();
        });
    }

    /**
     * Get cached model by ID
     */
    public static function findCached(int $id): ?Model
    {
        return static::remember(
            "model_{$id}",
            fn () => static::find($id),
            1800 // 30 minutes
        );
    }

    /**
     * Get cached models by IDs
     */
    public static function findManyCached(array $ids): \Illuminate\Database\Eloquent\Collection
    {
        return static::remember(
            'models_' . md5(serialize($ids)),
            fn () => static::whereIn('id', $ids)->get(),
            1800
        );
    }

    /**
     * Get cached count
     */
    public static function countCached(string $key = 'count'): int
    {
        return static::remember(
            "count_{$key}",
            fn () => static::count(),
            3600
        );
    }

    /**
     * Get cached active models
     */
    public static function activeCached(): \Illuminate\Database\Eloquent\Collection
    {
        return static::remember(
            'active_models',
            fn () => static::where('status', 'active')->get(),
            1800
        );
    }

    /**
     * Get cached featured models
     */
    public static function featuredCached(): \Illuminate\Database\Eloquent\Collection
    {
        return static::remember(
            'featured_models',
            fn () => static::where('is_featured', true)->get(),
            1800
        );
    }
}
