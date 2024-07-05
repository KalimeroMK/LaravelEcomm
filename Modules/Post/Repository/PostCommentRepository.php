<?php

namespace Modules\Post\Repository;

use Illuminate\Support\Collection;
use Modules\Core\Repositories\Repository;
use Modules\Post\Models\PostComment;

class PostCommentRepository extends Repository
{
    public $model = PostComment::class;

    public function findAll(): Collection
    {
        return $this->model::with('user_info', 'user', 'post')->get();
    }
}
