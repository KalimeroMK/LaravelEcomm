<?php

declare(strict_types=1);

namespace Modules\Brand\Actions;

use Illuminate\Database\Eloquent\Model;
use Modules\Brand\DTOs\BrandDTO;
use Modules\Brand\Models\Brand;
use Modules\Brand\Repository\BrandRepository;

readonly class UpdateBrandAction
{
    public function __construct(private BrandRepository $repository)
    {
    }

    public function execute(BrandDTO $dto): Model
    {
        $brand = $this->repository->findById($dto->id);
        /** @var Brand $brand */
        $brand->update([
            'title' => $dto->title,
            'status' => $dto->status,
        ]);
        // Handle Spatie media upload
        if ($dto->images && is_array($dto->images)) {
            $brand->clearMediaCollection('brand');
            foreach ($dto->images as $image) {
                $brand->addMedia($image)->preservingOriginal()->toMediaCollection('brand');
            }
        }
        return $brand;
    }
}
