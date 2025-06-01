<?php

declare(strict_types=1);

namespace Modules\Brand\Actions;

use Modules\Brand\DTOs\BrandDTO;
use Modules\Brand\Models\Brand;
use Modules\Brand\Repository\BrandRepository;

readonly class CreateBrandAction
{
    public function __construct(private BrandRepository $repository) {}

    public function execute(BrandDTO $dto): Brand
    {
        return $this->repository->create([
            'title' => $dto->title,
            'slug' => $dto->slug,
            'status' => $dto->status,
        ]);
    }
}
