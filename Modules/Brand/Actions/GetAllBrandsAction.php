<?php

declare(strict_types=1);

namespace Modules\Brand\Actions;

use Illuminate\Support\Collection;
use Modules\Brand\Repository\BrandRepository;

readonly class GetAllBrandsAction
{
    public function __construct(private BrandRepository $repository) {}

    public function execute(): Collection
    {
        return $this->repository->findAll();
    }
}
