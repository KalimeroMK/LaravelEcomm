<?php

namespace Modules\Size\Repository;

use Modules\Core\Repositories\Repository;
use Modules\Size\Models\Size;

class SizesRepository extends Repository
{
    public $model = Size::class;
    
    /**
     * @return mixed
     */
    public function findAll(): mixed
    {
        return $this->model::get();
    }
}