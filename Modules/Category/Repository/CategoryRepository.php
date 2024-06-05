<?php

namespace Modules\Category\Repository;

use Illuminate\Support\Collection;
use Modules\Category\Models\Category;
use Modules\Core\Repositories\Repository;

class CategoryRepository extends Repository
{
    /**
     * @var string
     */
    public $model = Category::class;

    /**
     * @return Collection
     */
    public function findAll(): Collection
    {
        return $this->model::get();
    }
}
