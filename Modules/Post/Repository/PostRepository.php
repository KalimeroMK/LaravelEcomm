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
        return $this->model::with('categories', 'comments', 'post_comments', 'post_tag', 'author_info')->paginate(10);
    }
}