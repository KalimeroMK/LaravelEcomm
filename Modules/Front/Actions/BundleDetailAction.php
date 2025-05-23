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
        $related = Product::where('status', 'active')->orderBy('id', 'DESC')->limit(3)->get();

        return [
            'bundle' => $bundle,
            'related' => $related,
        ];
    }
}
