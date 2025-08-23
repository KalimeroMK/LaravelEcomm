<?php

declare(strict_types=1);

namespace Modules\Brand\Actions;

use Illuminate\Database\Eloquent\Model;
use Modules\Brand\DTOs\BrandDTO;
use Modules\Brand\Repository\BrandRepository;

readonly class UpdateBrandAction
{
    public function __construct(private BrandRepository $repository) {}

    public function execute(BrandDTO $dto): Model
    {
        $brand = $this->repository->findById($dto->id);

        $brand->update([
            'title' => $dto->title,
            'slug' => $dto->slug ?? $brand->slug,
            'status' => $dto->status ?? $brand->status,
        ]);

        return $brand;
    }
}
