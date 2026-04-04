<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Illuminate\Support\Facades\Cache;
use Modules\Banner\Repository\BannerRepository;
use Modules\Post\Repository\PostRepository;
use Modules\Product\Repository\ProductRepository;

class IndexAction
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly PostRepository $postRepository,
        private readonly BannerRepository $bannerRepository,
    ) {}

    public function __invoke(): array
    {
        $featured_products = Cache::remember('featured_products', 3600, fn () => $this->productRepository->getFeatured(4));

        $latest_products = Cache::remember('latest_products', 1800, fn () => $this->productRepository->getLatest(4));

        $hot_products = Cache::remember('hot_products', 1800, fn () => $this->productRepository->getLatest(4, 4));

        $posts = Cache::remember('active_posts_recent', 3600, fn () => $this->postRepository->getRecent(3));

        // Banner uses scopeActive() — filtered at DB level, no PHP filter() needed.
        $banners = Cache::remember('active_banners_with_categories', 7200, fn () => $this->bannerRepository->getActive());

        return [
            'featured_products' => $featured_products,
            'posts'             => $posts,
            'banners'           => $banners,
            'latest_products'   => $latest_products,
            'hot_products'      => $hot_products,
        ];
    }
}
