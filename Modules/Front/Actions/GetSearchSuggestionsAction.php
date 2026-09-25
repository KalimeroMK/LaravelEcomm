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
        'phone' => 'smartphone',
        'tv' => 'television',
        'pc' => 'personal computer',
    ];

    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly BrandRepository $brandRepository,
    ) {}

    public function __invoke(string $query): array
    {
        // Hit on every keystroke: cache briefly and fetch only the titles —
        // no relations, no pagination COUNT, no unbounded ->get().
        return \Illuminate\Support\Facades\Cache::remember(
            'search_suggestions_'.md5(mb_strtolower($query)),
            300,
            fn (): array => [
                'popular_terms' => \Modules\Product\Models\Product::query()
                    ->where('status', 'active')
                    ->where('title', 'like', "%{$query}%")
                    ->limit(5)
                    ->pluck('title')
                    ->toArray(),
                'categories' => Category::query()
                    ->active()
                    ->where('title', 'like', "%{$query}%")
                    ->limit(3)
                    ->pluck('title')
                    ->toArray(),
                'brands' => \Modules\Brand\Models\Brand::query()
                    ->where('title', 'like', "%{$query}%")
                    ->limit(3)
                    ->pluck('title')
                    ->toArray(),
                'suggested_query' => self::QUERY_CORRECTIONS[$query] ?? $query,
            ]
        );
    }
}
