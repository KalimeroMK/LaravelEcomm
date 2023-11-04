<?php

namespace Modules\Post\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Post\Models\PostComment;
use Modules\User\Models\User;

class PostCommentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('comment-list');
    }

    public function view(User $user): bool
    {
        return $user->can('comment-list');
    }

    public function create(User $user): bool
    {
        return $user->can('comment-create');
    }

    public function update(User $user, PostComment $comment): bool
    {
        return $user->can('comment-update');
    }

    public function delete(User $user, PostComment $comment): bool
    {
        return $user->can('comment-delete');
    }
}
