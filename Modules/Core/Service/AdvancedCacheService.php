<?php

declare(strict_types=1);

namespace Modules\Core\Service;

use Closure;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class AdvancedCacheService
{
    protected array $cacheConfig;

    protected array $tags = [];

    public function __construct()
    {
        $this->cacheConfig = config('cache.advanced', [
            'default_ttl' => 3600, // 1 hour
            'short_ttl' => 900,    // 15 minutes
            'long_ttl' => 86400,   // 24 hours
            'very_long_ttl' => 604800, // 7 days
        ]);
    }

    /**
     * Remember data with intelligent caching
     */
    public function remember(string $key, Closure $callback, ?int $ttl = null, array $tags = []): mixed
    {
        $ttl = $ttl ?? $this->cacheConfig['default_ttl'];

        try {
            if ($tags !== []) {
                return Cache::tags($tags)->remember($key, $ttl, $callback);
            }

            return Cache::remember($key, $ttl, $callback);
        } catch (Exception $e) {
            Log::warning("Cache error for key {$key}: ".$e->getMessage());

            return $callback();
        }
    }

    /**
     * Remember data with model-based invalidation
     */
    public function rememberModel(string $key, Model $model, $callback, ?int $ttl = null): mixed
    {
        $tags = [$this->getModelTag($model)];

        return $this->remember($key, $callback, $ttl, $tags);
    }

    /**
     * Remember data with collection-based invalidation
     */
    public function rememberCollection(string $key, Collection $models, $callback, ?int $ttl = null): mixed
    {
        $tags = $models->map(fn ($model): string => $this->getModelTag($model))->toArray();

        return $this->remember($key, $callback, $ttl, $tags);
    }

    /**
     * Cache with automatic warming
     */
    public function rememberWithWarming(string $key, $callback, ?int $ttl = null, bool $warmUp = true): mixed
    {
        $data = $this->remember($key, $callback, $ttl);

        if ($warmUp && $ttl > 300) { // Only warm up if TTL > 5 minutes
            $this->scheduleWarmUp($key, $callback, $ttl);
        }

        return $data;
    }

    /**
     * Intelligent cache invalidation
     */
    public function invalidateModel(Model $model): void
    {
        $tag = $this->getModelTag($model);
        $this->invalidateByTag($tag);
    }

    /**
     * Invalidate cache by tag
     */
    public function invalidateByTag(string $tag): void
    {
        try {
            Cache::tags([$tag])->flush();
        } catch (Exception $e) {
            Log::warning("Cache invalidation error for tag {$tag}: ".$e->getMessage());
        }
    }

    /**
     * Invalidate multiple tags
     */
    public function invalidateByTags(array $tags): void
    {
        try {
            Cache::tags($tags)->flush();
        } catch (Exception $e) {
            Log::warning('Cache invalidation error for tags: '.implode(', ', $tags).' - '.$e->getMessage());
        }
    }

    /**
     * Clear all caches
     */
    public function clearAll(): void
    {
        try {
            Cache::flush();
        } catch (Exception $e) {
            Log::warning('Cache clear all error: '.$e->getMessage());
        }
    }

    /**
     * Get cache statistics
     */
    public function getStats(): array
    {
        try {
            if (config('cache.default') === 'redis') {
                $info = Redis::info();

                return [
                    'used_memory' => $info['used_memory_human'] ?? 'N/A',
                    'connected_clients' => $info['connected_clients'] ?? 'N/A',
                    'total_commands_processed' => $info['total_commands_processed'] ?? 'N/A',
                    'keyspace_hits' => $info['keyspace_hits'] ?? 'N/A',
                    'keyspace_misses' => $info['keyspace_misses'] ?? 'N/A',
                    'hit_rate' => $this->calculateHitRate($info),
                ];
            }

            return ['driver' => config('cache.default')];
        } catch (Exception $e) {
            Log::warning('Cache stats error: '.$e->getMessage());

            return ['error' => 'Unable to retrieve cache statistics'];
        }
    }

    /**
     * Warm up critical caches
     */
    public function warmUpCriticalData(): void
    {
        $this->warmUpProducts();
        $this->warmUpCategories();
        $this->warmUpBrands();
        $this->warmUpSettings();
        $this->warmUpUserSessions();
    }

    /**
     * Warm up product caches
     */
    public function warmUpProducts(): void
    {
        $this->remember('products:featured', function () {
            return \Modules\Product\Models\Product::where('status', 'active')
                ->where('is_featured', true)
                ->with(['brand', 'categories', 'media'])
                ->limit(20)
                ->get();
        }, $this->cacheConfig['long_ttl'], ['products']);

        $this->remember('products:latest', function () {
            return \Modules\Product\Models\Product::where('status', 'active')
                ->with(['brand', 'categories', 'media'])
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();
        }, $this->cacheConfig['short_ttl'], ['products']);

        $this->remember('products:deals', function () {
            return \Modules\Product\Models\Product::where('status', 'active')
                ->where('d_deal', true)
                ->where('special_price_start', '<=', now())
                ->where('special_price_end', '>=', now())
                ->with(['brand', 'categories', 'media'])
                ->get();
        }, $this->cacheConfig['short_ttl'], ['products']);
    }

    /**
     * Warm up category caches
     */
    public function warmUpCategories(): void
    {
        $this->remember('categories:tree', function () {
            return \Modules\Category\Models\Category::where('status', 'active')
                ->with('children')
                ->whereNull('parent_id')
                ->get();
        }, $this->cacheConfig['very_long_ttl'], ['categories']);

        $this->remember('categories:active', function () {
            return \Modules\Category\Models\Category::where('status', 'active')
                ->select('id', 'name', 'slug')
                ->get();
        }, $this->cacheConfig['long_ttl'], ['categories']);
    }

    /**
     * Warm up brand caches
     */
    public function warmUpBrands(): void
    {
        $this->remember('brands:active', function () {
            return \Modules\Brand\Models\Brand::where('status', 'active')
                ->select('id', 'name', 'slug', 'logo')
                ->get();
        }, $this->cacheConfig['long_ttl'], ['brands']);
    }

    /**
     * Warm up settings caches
     */
    public function warmUpSettings(): void
    {
        $this->remember('settings:all', function () {
            return \Modules\Settings\Models\Setting::pluck('value', 'key');
        }, $this->cacheConfig['very_long_ttl'], ['settings']);
    }

    /**
     * Warm up user session caches
     */
    public function warmUpUserSessions(): void
    {
        // Warm up active user sessions
        $this->remember('users:active_sessions', function () {
            return \Modules\User\Models\User::where('status', 'active')
                ->where('last_activity_at', '>=', now()->subHours(24))
                ->select('id', 'name', 'email', 'last_activity_at')
                ->limit(100)
                ->get();
        }, $this->cacheConfig['short_ttl'], ['users']);

        // Warm up user preferences
        $this->remember('users:preferences:default', function (): array {
            return [
                'theme' => 'light',
                'language' => 'en',
                'currency' => 'USD',
                'timezone' => 'UTC',
                'notifications' => [
                    'email' => true,
                    'sms' => false,
                    'push' => true,
                ],
            ];
        }, $this->cacheConfig['very_long_ttl'], ['users', 'preferences']);

        // Warm up user roles and permissions
        $this->remember('users:roles:permissions', function () {
            return \Spatie\Permission\Models\Role::with('permissions')
                ->get()
                ->mapWithKeys(function ($role): array {
                    return [$role->name => $role->permissions->pluck('name')];
                });
        }, $this->cacheConfig['long_ttl'], ['users', 'roles', 'permissions']);

        // Warm up user statistics
        $this->remember('users:statistics', function (): array {
            return [
                'total_users' => \Modules\User\Models\User::count(),
                'active_users' => \Modules\User\Models\User::where('status', 'active')->count(),
                'new_users_today' => \Modules\User\Models\User::whereDate('created_at', today())->count(),
                'new_users_this_week' => \Modules\User\Models\User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'new_users_this_month' => \Modules\User\Models\User::whereMonth('created_at', now()->month)->count(),
            ];
        }, $this->cacheConfig['short_ttl'], ['users', 'statistics']);

        // Warm up user cart data for active users
        $this->remember('users:carts:active', function () {
            return \Modules\Cart\Models\Cart::with(['user', 'product'])
                ->whereHas('user', function ($query): void {
                    $query->where('status', 'active')
                        ->where('last_activity_at', '>=', now()->subHours(24));
                })
                ->select('user_id', 'product_id', 'quantity', 'created_at')
                ->limit(200)
                ->get()
                ->groupBy('user_id');
        }, $this->cacheConfig['short_ttl'], ['users', 'carts']);

        // Warm up user wishlist data
        $this->remember('users:wishlists:active', function () {
            return \Modules\Billing\Models\Wishlist::with(['user', 'product'])
                ->whereHas('user', function ($query): void {
                    $query->where('status', 'active');
                })
                ->select('user_id', 'product_id', 'created_at')
                ->limit(200)
                ->get()
                ->groupBy('user_id');
        }, $this->cacheConfig['short_ttl'], ['users', 'wishlists']);

        // Warm up user order history for recent orders
        $this->remember('users:orders:recent', function () {
            return \Modules\Order\Models\Order::with(['user'])
                ->whereHas('user', function ($query): void {
                    $query->where('status', 'active');
                })
                ->where('created_at', '>=', now()->subDays(30))
                ->select('user_id', 'order_number', 'status', 'total_amount', 'created_at')
                ->orderBy('created_at', 'desc')
                ->limit(100)
                ->get()
                ->groupBy('user_id');
        }, $this->cacheConfig['short_ttl'], ['users', 'orders']);
    }

    /**
     * Cache with compression for large data
     */
    public function rememberCompressed(string $key, $callback, ?int $ttl = null): mixed
    {
        $ttl = $ttl ?? $this->cacheConfig['default_ttl'];

        try {
            $data = $callback();
            $compressed = gzcompress(serialize($data));

            Cache::put($key.':compressed', $compressed, $ttl);

            return $data;
        } catch (Exception $e) {
            Log::warning("Compressed cache error for key {$key}: ".$e->getMessage());

            return $callback();
        }
    }

    /**
     * Get compressed cache data
     */
    public function getCompressed(string $key): mixed
    {
        try {
            $compressed = Cache::get($key.':compressed');

            if ($compressed) {
                return unserialize(gzuncompress($compressed));
            }

            return null;
        } catch (Exception $e) {
            Log::warning("Get compressed cache error for key {$key}: ".$e->getMessage());

            return null;
        }
    }

    /**
     * Cache with automatic refresh
     */
    public function rememberWithRefresh(string $key, $callback, ?int $ttl = null, int $refreshThreshold = 300): mixed
    {
        $ttl = $ttl ?? $this->cacheConfig['default_ttl'];

        $data = $this->remember($key, $callback, $ttl);

        // Check if cache needs refresh
        $remainingTtl = Cache::get($key.':ttl', $ttl);

        if ($remainingTtl <= $refreshThreshold) {
            // Refresh cache in background
            $this->refreshInBackground($key, $callback, $ttl);
        }

        return $data;
    }

    /**
     * Get model tag for cache invalidation
     */
    protected function getModelTag(Model $model): string
    {
        return mb_strtolower(class_basename($model)).':'.$model->getKey();
    }

    /**
     * Schedule cache warm up
     */
    protected function scheduleWarmUp(string $key, $callback, int $ttl): void
    {
        // Schedule warm up when cache is about to expire
        $warmUpTime = $ttl - 300; // 5 minutes before expiry

        if ($warmUpTime > 0) {
            // This would typically use a job queue
            // For now, we'll just log it
            Log::info("Scheduled warm up for key: {$key} in {$warmUpTime} seconds");
        }
    }

    /**
     * Calculate cache hit rate
     */
    protected function calculateHitRate(array $info): string
    {
        $hits = $info['keyspace_hits'] ?? 0;
        $misses = $info['keyspace_misses'] ?? 0;
        $total = $hits + $misses;

        if ($total === 0) {
            return '0%';
        }

        $hitRate = ($hits / $total) * 100;

        return number_format($hitRate, 2).'%';
    }

    /**
     * Refresh cache in background
     */
    protected function refreshInBackground(string $key, $callback, int $ttl): void
    {
        // This would typically use a job queue
        // For now, we'll just refresh immediately
        try {
            $newData = $callback();
            Cache::put($key, $newData, $ttl);
            Cache::put($key.':ttl', $ttl, $ttl);
        } catch (Exception $e) {
            Log::warning("Background refresh error for key {$key}: ".$e->getMessage());
        }
    }
}
