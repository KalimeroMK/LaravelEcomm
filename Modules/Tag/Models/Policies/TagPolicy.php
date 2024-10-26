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
        return $user->can('tag-list');
    }

    public function view(User $user): bool
    {
        return $user->can('tag-list');
    }

    public function create(User $user): bool
    {
        return $user->can('tag-create');
    }

    public function update(User $user, Tag $tag): bool
    {
        return $user->can('tag-update');
    }

    public function delete(User $user, Tag $tag): bool
    {
        return $user->can('tag-delete');
    }
}
