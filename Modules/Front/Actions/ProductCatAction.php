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

        // Child category cards: real product count and a cover image taken
        // from the first active product (categories carry no photo column, so
        // the templates' $childCat->photo used to always fall back to the
        // static grey placeholder).
        $childCategories = Cache::remember(
            "category_children_{$category->id}",
            1800,
            function () use ($category) {
                $children = $category->children()
                    ->withCount(['products' => fn ($q) => $q->where('status', 'active')])
                    ->get();

                $children->each(function ($child): void {
                    $cover = $child->products()
                        ->where('status', 'active')
                        ->whereHas('media')
                        ->with('media')
                        ->first();
                    $child->setAttribute('photo', $cover?->image_thumb_url);
                });

                return $children;
            }
        );

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
