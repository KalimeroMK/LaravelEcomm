<?php

declare(strict_types=1);

namespace Modules\Product\Services;

use Illuminate\Support\Facades\Cache;
use Modules\Product\Models\Product;

class RecentlyViewedService
{
    private const CACHE_TTL = 43200; // 30 days in minutes
    private const MAX_ITEMS = 20;

    /**
     * Get cache key for user or session.
     */
    private function getCacheKey(?int $userId = null): string
    {
        if ($userId) {
            return 'recently_viewed_user_' . $userId;
        }
        
        $sessionId = session()->getId();
        return 'recently_viewed_session_' . $sessionId;
    }

    /**
     * Add product to recently viewed list.
     */
    public function addProduct(int $productId, ?int $userId = null): void
    {
        $key = $this->getCacheKey($userId);
        $products = Cache::get($key, []);
        
        // Remove if already exists (to move to front)
        $products = array_diff($products, [$productId]);
        
        // Add to front of array
        array_unshift($products, $productId);
        
        // Limit to max items
        $products = array_slice($products, 0, self::MAX_ITEMS);
        
        Cache::put($key, $products, self::CACHE_TTL);
    }

    /**
     * Get recently viewed products.
     */
    public function getProducts(?int $userId = null, int $limit = 8): array
    {
        $key = $this->getCacheKey($userId);
        $productIds = Cache::get($key, []);
        
        if (empty($productIds)) {
            return [];
        }
        
        // Limit the number of products
        $productIds = array_slice($productIds, 0, $limit);
        
        // Get active products
        $products = Product::whereIn('id', $productIds)
            ->where('status', 'active')
            ->get();
        
        // Sort by original order
        $sortedProducts = [];
        foreach ($productIds as $id) {
            $product = $products->firstWhere('id', $id);
            if ($product) {
                $sortedProducts[] = $product;
            }
        }
        
        return $sortedProducts;
    }

    /**
     * Get recently viewed products for current user/session.
     */
    public function getForCurrentUser(int $limit = 8): array
    {
        $userId = auth()->id();
        return $this->getProducts($userId, $limit);
    }

    /**
     * Clear recently viewed products.
     */
    public function clear(?int $userId = null): void
    {
        $key = $this->getCacheKey($userId);
        Cache::forget($key);
    }

    /**
     * Get count of recently viewed products.
     */
    public function getCount(?int $userId = null): int
    {
        $key = $this->getCacheKey($userId);
        $products = Cache::get($key, []);
        return count($products);
    }

    /**
     * Migrate session data to user after login.
     */
    public function migrateToUser(int $userId): void
    {
        $sessionKey = $this->getCacheKey();
        $userKey = $this->getCacheKey($userId);
        
        $sessionProducts = Cache::get($sessionKey, []);
        $userProducts = Cache::get($userKey, []);
        
        // Merge and remove duplicates
        $merged = array_unique(array_merge($userProducts, $sessionProducts));
        $merged = array_slice($merged, 0, self::MAX_ITEMS);
        
        Cache::put($userKey, $merged, self::CACHE_TTL);
        Cache::forget($sessionKey);
    }
}
