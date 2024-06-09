<?php

namespace Modules\Banner\Service;

use Illuminate\Database\Eloquent\Model;
use Modules\Banner\Repository\BannerRepository;
use Modules\Core\Service\CoreService;
use Modules\Core\Traits\ImageUpload;

class BannerService extends CoreService
{
    use ImageUpload;

    public BannerRepository $banner_repository;

    public function __construct(BannerRepository $banner_repository)
    {
        parent::__construct($banner_repository);
        $this->banner_repository = $banner_repository;
    }

    /**
     * Create a new banner with possible media files.
     *
     * @param array<string, mixed> $data The data for creating the banner.
     * @return Model The newly created banner model.
     */
    public function create(array $data): Model
    {
        $banner = $this->banner_repository->create($data);

        // Handle image uploads
        if (request()->hasFile('images')) {
            $banner->addMultipleMediaFromRequest(['images'])
                ->each(function ($fileAdder) {
                    $fileAdder->preservingOriginal()->toMediaCollection('banner');
                });
        }

        return $banner;
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
        $banner = $this->banner_repository->findById($id);

        $banner->update($data);

        // Check for new image uploads and handle them
        if (request()->hasFile('images')) {
            $banner->clearMediaCollection('banner'); // Optionally clear existing media
            $banner->addMultipleMediaFromRequest(['images'])
                ->each(function ($fileAdder) {
                    $fileAdder->preservingOriginal()->toMediaCollection('banner');
                });
        }

        return $banner;
    }
}
