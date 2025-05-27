<?php

declare(strict_types=1);

namespace Modules\Brand\Actions;

use Illuminate\Database\Eloquent\Model;
use Modules\Brand\DTOs\BrandDTO;
use Modules\Brand\Models\Brand;
use Modules\Brand\Repository\BrandRepository;

class UpdateBrandAction
{
    private BrandRepository $repository;

    public function __construct(BrandRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(BrandDTO $dto): Model
    {
        $brand = $this->repository->findById($dto->id);
        /** @var Brand $brand */
        $brand->update([
            'title' => $dto->title,
            'slug' => $dto->slug ?? $brand->slug,
            'status' => $dto->status,
        ]);

        if (! empty($dto->images)) {
            $brand->clearMediaCollection('brand');
            $brand->addMultipleMediaFromRequest(['images'])
                ->each(fn ($fileAdder) => $fileAdder->preservingOriginal()->toMediaCollection('brand'));
        }

        return $brand;
    }
}
