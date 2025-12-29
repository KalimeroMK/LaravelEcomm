<?php

declare(strict_types=1);

namespace Modules\Brand\Actions;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Brand\Repository\BrandRepository;

readonly class SearchBrandsAction
{
    public function __construct(private BrandRepository $repository) {}

    /**
     * Search brands with optional filters.
     *
     * @param  array<string, mixed>  $filters
     * @return Collection|LengthAwarePaginator
     */
    public function execute(array $filters = [])
    {
        return $this->repository->search($filters);
    }
}
