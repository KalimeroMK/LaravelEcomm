<?php

declare(strict_types=1);

namespace Modules\Brand\Service;

use Illuminate\Database\Eloquent\Model;
use Modules\Brand\Repository\BrandRepository;
use Modules\Core\Service\CoreService;

class BrandService extends CoreService
{
    public BrandRepository $brand_repository;

    public function __construct(BrandRepository $brand_repository)
    {
        parent::__construct($brand_repository);
        $this->brand_repository = $brand_repository;
    }

    /**
     * Store a new attribute.
     *
     * @param  array<string, mixed>  $data  The data to create the attribute.
     * @return Model|null
     */
    /**
     * Create a new brand with possible media files.
     *
     * @param  array<string, mixed>  $data  The data for creating the brand.
     * @return Model The newly created brand model.
     */
    public function createWithMedia(array $data): Model
    {
        $brand = $this->brand_repository->create($data);

        // Handle image uploads
        if (array_key_exists('images', $data)) {
            $brand->clearMediaCollection('brand');
            $brand->addMultipleMediaFromRequest(['images'])
                ->each(function ($fileAdder): void {
                    $fileAdder->preservingOriginal()->toMediaCollection('brand');
                });
        }

        return $brand;
    }

    /**
     * Update an existing brand and handle media uploads.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateWithMedia(int $id, array $data): Model
    {
        $brand = $this->brand_repository->findById($id);
        $brand->update($data);
        // Handle image uploads
        if (array_key_exists('images', $data)) {
            $brand->clearMediaCollection('brand');
            $brand->addMultipleMediaFromRequest(['images'])
                ->each(function ($fileAdder): void {
                    $fileAdder->preservingOriginal()->toMediaCollection('brand');
                });
        }

        return $brand;
    }

    public function search(array $data): mixed
    {
        return $this->brand_repository->search($data);
    }
}
