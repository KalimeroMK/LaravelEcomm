<?php

namespace Modules\Brand\Service;

use Exception;
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
        try {
            return $this->brand_repository->create(
                collect($data)->except(['photo'])->toArray() + [
                    'photo' => $this->verifyAndStoreImage($data['photo']),
                ]
            );
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $id
     *
     * @return mixed
     */
    public function edit($id): mixed
    {
        try {
            return $this->brand_repository->findById($id);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $id
     *
     * @return mixed
     */
    public function show($id): mixed
    {
        try {
            return $this->brand_repository->findById($id);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $id
     * @param $data
     *
     * @return mixed|string
     */
    public function update($id, $data): mixed
    {
        try {
            if ( ! empty($data['photo'])) {
                return $this->brand_repository->update(
                    (int)$id,
                    collect($data)->except(['photo'])->toArray() + [
                        'photo' => $this->verifyAndStoreImage($data['photo']),
                    ]
                );
            }
            
            return $this->brand_repository->update((int)$id, $data);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $id
     *
     * @return string|void
     */
    
    public function destroy($id)
    {
        try {
            $this->brand_repository->delete($id);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @return mixed|string
     */
    public function getAll(): mixed
    {
        try {
            return $this->brand_repository->findAll();
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param  array  $data
     *
     * @return mixed|string
     */
    public function search(array $data): mixed
    {
        try {
            return $this->brand_repository->search($data);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
}