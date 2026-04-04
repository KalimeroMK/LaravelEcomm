<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Illuminate\Support\Facades\Cache;
use Modules\Category\Repository\CategoryRepository;
use Modules\Product\Repository\ProductRepository;

class ProductCatAction
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly ProductRepository $productRepository,
    ) {}

    public function __invoke(string $slug): array
    {
        $category = $this->categoryRepository->findBySlug($slug);

        if (! $category) {
            return [
                'category'        => null,
                'childCategories' => collect(),
                'products'        => collect(),
                'recentProducts'  => collect(),
                'error'           => 'Category not found',
            ];
        }

        $childCategories = $category->children;

        $products = $childCategories->isEmpty()
            ? $category->products()->where('status', 'active')->with(['brand', 'media'])->paginate(12)
            : collect();

        $recentProducts = Cache::remember('recent_products_sidebar', 1800, fn () => $this->productRepository->getRecent(4));

        return [
            'category'        => $category,
            'childCategories' => $childCategories,
            'products'        => $products,
            'recentProducts'  => $recentProducts,
        ];
    }
}
