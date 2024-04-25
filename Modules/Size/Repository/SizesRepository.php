<?php

namespace Modules\Size\Repository;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Repositories\Repository;
use Modules\Size\Models\Size;

class SizesRepository extends Repository
{
    public Model $model = Size::class;

    /**
     * @return mixed
     */
    public function findAll(): mixed
    {
        return $this->model::get();
    }
}