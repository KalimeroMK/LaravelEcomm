<?php

namespace Modules\Post\Repository;

use Modules\Core\Repositories\Repository;
use Modules\Post\Models\Post;

class PostRepository extends Repository
{
    public $model = Post::class;
    
    /**
     * @return mixed
     */
    public function findAll(): mixed
    {
        return $this->model::paginate(10);
    }
}