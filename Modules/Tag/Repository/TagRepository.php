<?php

namespace Modules\Tag\Repository;

use Modules\Core\Repositories\Repository;
use Modules\Tag\Models\Tag;

class TagRepository extends Repository
{
    public $model = Tag::class;

    /**
     * @return mixed
     */
    public function findAll(): mixed
    {
        return $this->model::orderBy('id', 'DESC')->get();
    }

}