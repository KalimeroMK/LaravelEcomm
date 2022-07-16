<?php

namespace Modules\Banner\Service;

use App\Traits\ImageUpload;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use LaravelIdea\Helper\Modules\Banner\Models\_IH_Banner_C;
use Modules\Banner\Models\Banner;
use Modules\Banner\Repository\BannerRepository;

class BannerService
{
    use ImageUpload;
    
    private BannerRepository $banner_repository;
    
    public function __construct(BannerRepository $banner_repository)
    {
        $this->banner_repository = $banner_repository;
    }
    
    /**
     * @param $data
     *
     * @return Collection|_IH_Banner_C|mixed|Banner|Banner[]|string
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
     * Make paths for storing images.
     *
     * @return object
     */
    public function makePaths(): object
    {
        $original  = public_path().'/uploads/images/banner/';
        $thumbnail = public_path().'/uploads/images/banner/thumbnails/';
        $medium    = public_path().'/uploads/images/banner/medium/';
        
        return (object)compact('original', 'thumbnail', 'medium');
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
            return $this->banner_repository->update($id, $data);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param $banner
     *
     * @return string|void
     */
    public function destroy($banner)
    {
        try {
            $this->banner_repository->delete($banner->id);
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
            return $this->banner_repository->findAll();
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
}