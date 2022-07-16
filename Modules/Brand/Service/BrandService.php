<?php

namespace Modules\Brand\Service;

use App\Traits\ImageUpload;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use LaravelIdea\Helper\Modules\Banner\Models\_IH_Banner_C;
use Modules\Banner\Models\Banner;
use Modules\Brand\Repository\BrandRepository;

class BrandService
{
    use ImageUpload;
    
    private BrandRepository $brand_repository;
    
    public function __construct(BrandRepository $brand_repository)
    {
        $this->brand_repository = $brand_repository;
    }
    
    /**
     * @param $data
     *
     * @return Collection|_IH_Banner_C|mixed|Banner|Banner[]
     */
    public function store($data): mixed
    {
        try {
            return $this->brand_repository->create($data);
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
    public function update($id, $data)
    {
        try {
            return $this->brand_repository->update($id, $data);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $banner
     *
     * @return string|void
     */
    
    public function destroy($banner)
    {
        try {
            $this->brand_repository->delete($banner->id);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @return mixed|string
     */
    public function getAll()
    {
        try {
            return $this->brand_repository->findAll();
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
}