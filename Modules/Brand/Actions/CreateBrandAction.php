<?php

declare(strict_types=1);

namespace Modules\Brand\Actions;

use Modules\Brand\DTOs\BrandDTO;
use Modules\Brand\Models\Brand;
use Modules\Brand\Repository\BrandRepository;

readonly class CreateBrandAction
{
    public function __construct(private BrandRepository $repository)
    {
    }

    public function execute(BrandDTO $dto): Brand
    {
        /** @var Brand $banner */

        $brand = $this->repository->create([
            'name' => $dto->title,
            'slug' => $dto->slug,
            'status' => $dto->status,
            'images' => $dto->images
        ]);

        if (!empty($dto->images) && is_array($dto->images)) {
            $brand->clearMediaCollection('brand');
            foreach ($dto->images as $image) {
                $brand->addMedia($image)
                    ->preservingOriginal()
                    ->toMediaCollection('brand');
            }
        }

        return $brand;
    }
}
