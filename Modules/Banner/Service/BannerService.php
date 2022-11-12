<?php

namespace Modules\Banner\Service;

use Exception;
use Modules\Banner\Exceptions\SearchException;
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
        try {
            return $this->banner_repository->create(
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
     * @return mixed|string
     */
    public function edit($id): mixed
    {
        try {
            return $this->banner_repository->findById($id);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $id
     *
     * @return mixed|string
     */
    public function show($id): mixed
    {
        try {
            return $this->banner_repository->findById($id);
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
                return $this->banner_repository->update(
                    (int)$id,
                    collect($data)->except(['photo'])->toArray() + [
                        'photo' => $this->verifyAndStoreImage($data['photo']),
                    ]
                );
            }
            
            return $this->banner_repository->update((int)$id, $data);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     *
     * @return string|void
     */
    public function destroy($id)
    {
        try {
            $this->banner_repository->delete($id);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @return mixed|string
     * @throws SearchException
     */
    public function getAll($data): mixed
    {
        try {
            return $this->banner_repository->search($data);
        } catch (Exception $exception) {
            throw new SearchException($exception);
        }
    }
}