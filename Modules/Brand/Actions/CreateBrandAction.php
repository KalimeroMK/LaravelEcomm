<?php

declare(strict_types=1);

namespace Modules\Brand\Actions;

use Modules\Brand\DTOs\BrandDTO;
use Modules\Brand\Models\Brand;
use Modules\Brand\Repository\BrandRepository;

class CreateBrandAction
{
    private BrandRepository $repository;

    public function __construct(BrandRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(BrandDTO $dto): Brand
    {
        /** @var Brand $brand */
        $brand = $this->repository->create([
            'title' => $dto->title,
            'slug' => $dto->slug,
            'status' => $dto->status,
        ]);

        if (!empty($dto->images)) {
            $brand->addMultipleMediaFromRequest(['images'])
                ->each(fn($fileAdder) => $fileAdder->preservingOriginal()->toMediaCollection('brand'));
        }

        return $brand;
    }
}
