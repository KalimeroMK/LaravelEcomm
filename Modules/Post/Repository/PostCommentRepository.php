<?php

namespace Modules\Post\Repository;

use Illuminate\Support\Facades\Auth;
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
    
    /**
     * @return mixed
     */
    public function findAllByUser(): mixed
    {
        return $this->model::with('user_info', 'user', 'post')->where('user_id', Auth::id())->paginate(10);
    }
}