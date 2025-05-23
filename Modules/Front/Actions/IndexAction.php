<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Cache;
use Modules\Banner\Models\Banner;
use Modules\Post\Models\Post;
use Modules\Product\Models\Product;

class IndexAction
{
    public function __invoke(): array
    {
        // Featured Products
        $featured_products = Cache::remember('featured_products', 86400, function () {
            return Product::with('categories')
                ->orderBy('price', 'desc')
                ->limit(4)
                ->get();
        });

        // Latest Products
        $latest_products = Cache::remember('latest_products', 86400, function () {
            return Product::with('categories')
                ->where('status', 'active')
                ->orderBy('id', 'desc')
                ->limit(4)
                ->get();
        });

        // Posts
        $posts = Post::where('status', 'active')
            ->orderBy('id', 'desc')
            ->limit(3)
            ->get();

        // Banners
        $banners = Cache::remember('active_banners', 86400, function () {
            return Banner::where('status', 'active')
                ->orderBy('id', 'desc')
                ->limit(3)
                ->get();
        });

        return [
            'featured_products' => $featured_products,
            'posts' => $posts,
            'banners' => $banners,
            'latest_products' => $latest_products,
            'hot_products' => $latest_products->splice(4),
        ];
    }
}
