<?php

declare(strict_types=1);

namespace Modules\Product\Actions;

use Illuminate\Support\Collection;
use Modules\Product\Repository\ProductRepository;

readonly class GetFeaturedProductsAction
{
    public function __construct(private ProductRepository $repository) {}

    public function execute(): Collection
    {
        // Filter in SQL instead of loading the whole catalog and filtering in PHP.
        return $this->repository->findFeatured();
    }
}
