<?php

namespace Modules\Post\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Post\Models\Post;
use Modules\User\Models\User;

class PostPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('post-list');
    }

    public function view(User $user): bool
    {
        return $user->can('post-list');
    }

    public function create(User $user): bool
    {
        return $user->can('post-create');
    }

    public function update(User $user, Post $post): bool
    {
        return $user->can('post-update') || $user->id == $post->added_by;
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->can('post-delete') || $user->id == $post->added_by;
    }
}
