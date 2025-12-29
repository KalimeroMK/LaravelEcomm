<?php

declare(strict_types=1);

namespace Modules\Brand\Actions;

use Modules\Brand\Models\Brand;
use Modules\Brand\Repository\BrandRepository;

readonly class FindBrandAction
{
    public function __construct(private BrandRepository $repository) {}

    public function execute(int $id): Brand
    {
        return $this->repository->findById($id);
    }
}
