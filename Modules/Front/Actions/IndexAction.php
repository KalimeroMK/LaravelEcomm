<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Illuminate\Support\Facades\Cache;
use Modules\Banner\Models\Banner;
use Modules\Post\Models\Post;
use Modules\Product\Models\Product;

class IndexAction
{
    public function __invoke(): array
    {
        // Featured Products with better caching and eager loading
        $featuredProductsCacheKey = 'featured_products_'.md5('active_price_desc_4');
        $featured_products = Cache::remember($featuredProductsCacheKey, 3600, function () {
            return Product::with(['categories', 'brand', 'tags'])
                ->where('status', 'active')
                ->where('is_featured', true)
                ->orderBy('price', 'desc')
                ->limit(4)
                ->get();
        });

        // Latest Products with better caching and eager loading
        $latestProductsCacheKey = 'latest_products_'.md5('active_created_desc_4');
        $latest_products = Cache::remember($latestProductsCacheKey, 1800, function () {
            return Product::with(['categories', 'brand', 'tags'])
                ->where('status', 'active')
                ->orderBy('id', 'desc')
                ->limit(4)
                ->get();
        });

        // Posts with caching
        $postsCacheKey = 'active_posts_'.md5('active_created_desc_3');
        $posts = Cache::remember($postsCacheKey, 3600, function () {
            return Post::where('status', 'active')
                ->orderBy('id', 'desc')
                ->limit(3)
                ->get();
        });

        // Banners with caching
        $bannersCacheKey = 'active_banners_with_categories';
        $banners = Cache::remember($bannersCacheKey, 7200, function () {
            return Banner::with('categories')
                ->get()
                ->filter(fn ($b): bool => $b->isActive());
        });

        // Hot products (next 4 after latest) with eager loading
        $hotProductsCacheKey = 'hot_products_'.md5('active_created_desc_4_8');
        $hot_products = Cache::remember($hotProductsCacheKey, 1800, function () {
            return Product::with(['categories', 'brand', 'tags'])
                ->where('status', 'active')
                ->orderBy('id', 'desc')
                ->offset(4)
                ->limit(4)
                ->get();
        });

        return [
            'featured_products' => $featured_products,
            'posts' => $posts,
            'banners' => $banners,
            'latest_products' => $latest_products,
            'hot_products' => $hot_products,
        ];
    }
}
