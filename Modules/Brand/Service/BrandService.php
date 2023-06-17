<?php

namespace Modules\Brand\Service;

use Modules\Brand\Repository\BrandRepository;
use Modules\Core\Service\CoreService;
use Modules\Core\Traits\ImageUpload;

class BrandService extends CoreService
{
    use ImageUpload;

    public BrandRepository $brand_repository;

    public function __construct(BrandRepository $brand_repository)
    {
        $this->brand_repository = $brand_repository;
    }

    /**
     * @param $data
     *
     * @return mixed
     */
    public function store($data): mixed
    {
            return $this->brand_repository->create(
                collect($data)->except(['photo'])->toArray() + [
                    'photo' => $this->verifyAndStoreImage($data['photo']),
                ]
            );
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function edit($id): mixed
    {
            return $this->brand_repository->findById($id);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function show($id): mixed
    {
        return $this->brand_repository->findById($id);
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
                return $this->brand_repository->update(
                    (int)$id,
                    collect($data)->except(['photo'])->toArray() + [
                        'photo' => $this->verifyAndStoreImage($data['photo']),
                    ]
                );
            }

            return $this->brand_repository->update((int)$id, $data);
    }

    /**
     * @param $id
     *
     * @return string|void
     */

    public function destroy($id)
    {
            $this->brand_repository->delete($id);
    }

    /**
     * @return mixed|string
     */
    public function getAll($data): mixed
    {
            return $this->brand_repository->search($data);
    }
}
