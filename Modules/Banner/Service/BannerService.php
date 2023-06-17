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
     * @param $data
     *
     * @return mixed
     */
    public function store($data): mixed
    {
            return $this->banner_repository->create(
                collect($data)->except(['photo'])->toArray() + [
                    'photo' => $this->verifyAndStoreImage($data['photo']),
                ]
            );
    }

    /**
     * @param $id
     *
     * @return mixed|string
     */
    public function edit($id): mixed
    {
        return $this->banner_repository->findById($id);
    }

    /**
     * @param $id
     *
     * @return mixed|string
     */
    public function show($id): mixed
    {
            return $this->banner_repository->findById($id);
    }

    /**
     * @param $id
     * @param $data
     *
     * @return mixed|string
     */
    public function update($id, $data): mixed
    {
            if ( ! empty($data['photo'])) {
                return $this->banner_repository->update(
                    (int)$id,
                    collect($data)->except(['photo'])->toArray() + [
                        'photo' => $this->verifyAndStoreImage($data['photo']),
                    ]
                );
            }

            return $this->banner_repository->update((int)$id, $data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     *
     * @return void
     */
    public function destroy($id)
    {

            $this->banner_repository->delete($id);
    }

    /**
     * @return mixed|string
     */
    public function getAll($data): mixed
    {
            return $this->banner_repository->search($data);
    }
}
