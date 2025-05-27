<?php

declare(strict_types=1);

namespace Modules\Brand\Actions;

use Modules\Brand\Repository\BrandRepository;

readonly class DeleteBrandAction
{
    public function __construct(
        private BrandRepository $repository
    ) {
    }

    public function execute(int $id): bool
    {
        $this->repository->destroy($id);
    }
}
