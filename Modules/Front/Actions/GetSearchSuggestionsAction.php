<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Modules\Brand\Repository\BrandRepository;
use Modules\Category\Models\Category;
use Modules\Category\Repository\CategoryRepository;
use Modules\Product\Repository\ProductRepository;

class GetSearchSuggestionsAction
{
    private const QUERY_CORRECTIONS = [
        'laptop' => 'laptop computer',
        'phone'  => 'smartphone',
        'tv'     => 'television',
        'pc'     => 'personal computer',
    ];

    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly BrandRepository $brandRepository,
    ) {}

    public function __invoke(string $query): array
    {
        $products = $this->productRepository->searchByTerm($query, 5);

        $categories = Category::where('title', 'like', "%{$query}%")->active()->get() ?? collect();

        $brands = $this->brandRepository->searchByTerm($query);

        return [
            'popular_terms'  => $products->pluck('title')->take(5)->toArray(),
            'categories'     => $categories->pluck('title')->take(3)->toArray(),
            'brands'         => $brands->pluck('title')->take(3)->toArray(),
            'suggested_query' => self::QUERY_CORRECTIONS[$query] ?? $query,
        ];
    }
}
