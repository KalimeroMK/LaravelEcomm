<?php

declare(strict_types=1);

namespace Modules\Product\Actions;

use Modules\Product\Models\Product;
use Modules\Product\Repository\ProductRepository;

readonly class FindProductBySlugAction
{
    public function __construct(private ProductRepository $repository) {}

    public function execute(string $slug): ?Product
    {
        return Product::whereSlug($slug)->first();
    }
}
