<?php

namespace Modules\Size\Repository;

use Illuminate\Support\Collection;
use Modules\Core\Repositories\Repository;
use Modules\Size\Models\Size;

class SizesRepository extends Repository
{
    public $model = Size::class;

    /**
     * @return Collection
     */
    public function findAll(): Collection
    {
        return $this->model::get();
    }
}