<?php

namespace Modules\Banner\Repository;

use Modules\Banner\Models\Banner;
use Modules\Core\Repositories\Repository;

class BannerRepository extends Repository
{
    public $model = Banner::class;
    
    /**
     * @return mixed
     */
    public function findAll(): mixed
    {
        return $this->model::paginate(10);
    }
    
}
