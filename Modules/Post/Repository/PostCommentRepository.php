<?php

namespace Modules\Post\Repository;

use Modules\Core\Repositories\Repository;
use Modules\Post\Models\PostComment;

class PostCommentRepository extends Repository
{
    
    public $model = PostComment::class;
    
    /**
     * @return mixed
     */
    public function findAll(): mixed
    {
        return $this->model::with('user_info', 'user', 'post')->paginate(10);
    }
    
}