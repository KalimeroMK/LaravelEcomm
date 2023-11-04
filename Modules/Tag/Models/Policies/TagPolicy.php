<?php

namespace Modules\Tag\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Tag\Models\Tag;
use Modules\User\Models\User;

class TagPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('tags-list');
    }

    public function view(User $user): bool
    {
        return $user->can('tags-list');
    }

    public function create(User $user): bool
    {
        return $user->can('tags-create');
    }

    public function update(User $user, Tag $tag): bool
    {
        return $user->can('tags-update');
    }

    public function delete(User $user, Tag $tag): bool
    {
        return $user->can('tags-delete');
    }
}