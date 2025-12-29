<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Modules\Bundle\Repository\BundleRepository;
use Modules\Product\Models\Product;

class BundleDetailAction
{
    protected BundleRepository $bundleRepository;

    public function __construct(BundleRepository $bundleRepository)
    {
        $this->bundleRepository = $bundleRepository;
    }

    public function __invoke(string $slug): array
    {
        $bundle = $this->bundleRepository->findBySlug($slug);

        if (! $bundle) {
            abort(404, 'Bundle not found');
        }

        // Load products relationship if not already loaded
        if (! $bundle->relationLoaded('products')) {
            $bundle->load('products');
        }

        $related = Product::with(['categories', 'brand', 'tags', 'attributeValues.attribute'])
            ->where('status', 'active')
            ->whereNotIn('id', $bundle->products->pluck('id'))
            ->orderBy('id', 'DESC')
            ->limit(8)
            ->get();

        return [
            'bundle' => $bundle,
            'related' => $related,
        ];
    }
}
