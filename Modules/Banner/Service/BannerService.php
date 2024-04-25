<?php

namespace Modules\Banner\Service;

use Modules\Banner\Repository\BannerRepository;
use Modules\Core\Service\CoreService;
use Modules\Core\Traits\ImageUpload;

class BannerService extends CoreService
{
    use ImageUpload;

    public BannerRepository $banner_repository;

    public function __construct(BannerRepository $banner_repository)
    {
        $this->banner_repository = $banner_repository;
    }

    /**
     * Store a new banner.
     *
     * @param  array<string, mixed>  $data  The data to create the attribute.
     * @return mixed
     */
    public function store(array $data): mixed
    {
        return $this->banner_repository->create(
            collect($data)->except(['photo'])->toArray() + [
                'photo' => $this->verifyAndStoreImage($data['photo']),
            ]
        );
    }

    /**
     * @param  int  $id
     *
     * @return mixed|string
     */
    public function edit(int $id): mixed
    {
        return $this->banner_repository->findById($id);
    }

    /**
     * @param  int  $id
     *
     * @return mixed|string
     */
    public function show(int $id): mixed
    {
        return $this->banner_repository->findById($id);
    }

    /**
     * Update an existing attribute.
     *
     * @param  int  $id  The banner ID to update.
     * @param  array<string, mixed>  $data  The data for updating the attribute.
     * @return mixed
     */
    public function update(int $id, array $data): mixed
    {
        if (!empty($data['photo'])) {
            return $this->banner_repository->update(
                (int) $id,
                collect($data)->except(['photo'])->toArray() + [
                    'photo' => $this->verifyAndStoreImage($data['photo']),
                ]
            );
        }

        return $this->banner_repository->update((int) $id, $data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return void
     */
    public function destroy(int $id): void
    {
        $this->banner_repository->delete($id);
    }

    /**
     * Store a new banner.
     *
     * @param  array<string, mixed>  $data  The data to create the attribute.
     * @return mixed
     */
    public function getAll(array $data): mixed
    {
        return $this->banner_repository->search($data);
    }
}
