<?php

namespace Modules\Size\Repository;

use Modules\Core\Repositories\Repository;
use Modules\Size\Models\Size;

class SizesRepository extends Repository
{
    public $model = Size::class;

    /**
     * @return object
     */
    public function findAll(): object
    {
        return $this->model::get();
    }
}