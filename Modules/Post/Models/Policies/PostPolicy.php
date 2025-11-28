<?php

declare(strict_types=1);

namespace Modules\Post\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Post\Models\Post;
use Modules\User\Models\User;

class PostPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function view(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function update(User $user, Post $post): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']) || $user->id === $post->added_by;
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']) || $user->id === $post->added_by;
    }
}
