<?php

namespace Modules\Brand\Service;

use Illuminate\Database\Eloquent\Model;
use Modules\Brand\Repository\BrandRepository;
use Modules\Core\Service\CoreService;
use Modules\Core\Traits\ImageUpload;

class BrandService extends CoreService
{
    use ImageUpload;

    public BrandRepository $brand_repository;

    public function __construct(BrandRepository $brand_repository)
    {
        parent::__construct($brand_repository);
        $this->brand_repository = $brand_repository;
    }

    /**
     * Store a new attribute.
     *
     * @param array<string, mixed> $data The data to create the attribute.
     * @return Model|null
     */
    /**
     * Create a new banner with possible media files.
     *
     * @param array<string, mixed> $data The data for creating the banner.
     * @return Model The newly created banner model.
     */
    public function create(array $data): Model
    {
        $brand = $this->brand_repository->create($data);

        // Handle image uploads
        if (request()->hasFile('images')) {
            $brand->addMultipleMediaFromRequest(['images'])
                ->each(function ($fileAdder) {
                    $fileAdder->preservingOriginal()->toMediaCollection('brand');
                });
        }

        return $brand;
    }

    /**
     * Update an existing banner with new data and possibly new media files.
     *
     * @param int $id The banner ID to update.
     * @param array<string, mixed> $data The data for updating the banner.
     * @return Model The updated banner model.
     */
    public function update(int $id, array $data): Model
    {
        $brand = $this->brand_repository->findById($id);

        $brand->update($data);

        // Check for new image uploads and handle them
        if (request()->hasFile('images')) {
            $brand->clearMediaCollection('brand'); // Optionally clear existing media
            $brand->addMultipleMediaFromRequest(['images'])
                ->each(function ($fileAdder) {
                    $fileAdder->preservingOriginal()->toMediaCollection('brand');
                });
        }

        return $brand;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function search(array $data): mixed
    {
        return $this->brand_repository->search($data);
    }
}
