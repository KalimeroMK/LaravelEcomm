<?php

declare(strict_types=1);

namespace Modules\Post\Repository;

use Illuminate\Support\Collection;
use Modules\Core\Interfaces\EloquentRepositoryInterface;
use Modules\Core\Repositories\EloquentRepository;
use Modules\Post\Models\PostComment;

class PostCommentRepository extends EloquentRepository implements EloquentRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(PostComment::class);
    }

    /**
     * Get all post comments with related user and post info.
     */
    public function findAll(): Collection
    {
        return (new $this->modelClass)->with('user_info', 'user', 'post')->get();
    }
}
