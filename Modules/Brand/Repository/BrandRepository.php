<?php

namespace Modules\Brand\Repository;

use Modules\Brand\Models\Brand;
use Modules\Core\Repositories\Repository;

class BrandRepository extends Repository
{
    public $model = Brand::class;
    
    /**
     * @return mixed
     */
    public function findAll(): mixed
    {
        return $this->model::get();
    }
}
