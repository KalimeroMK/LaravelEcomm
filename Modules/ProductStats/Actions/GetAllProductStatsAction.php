<?php

declare(strict_types=1);

namespace Modules\ProductStats\Actions;

use Illuminate\Support\Collection;
use Modules\ProductStats\Repository\ProductStatsRepository;

readonly class GetAllProductStatsAction
{
    public function __construct(private ProductStatsRepository $repository) {}

    public function execute(array $filters = []): Collection
    {
        return $this->repository->getProductStats($filters);
    }
}
