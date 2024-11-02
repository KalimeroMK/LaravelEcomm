<?php

namespace Modules\Attribute\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Attribute\Models\Attribute;
use Modules\User\Models\User;

class AttributePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('attribute-list');
    }

    public function view(User $user): bool
    {
        return $user->can('attribute-list');
    }

    public function create(User $user): bool
    {
        return $user->can('attribute-create');
    }

    public function update(User $user, Attribute $attribute): bool
    {
        return $user->can('attribute-create');
    }

    public function delete(User $user, Attribute $attribute): bool
    {
        return $user->can('attribute-delete');
    }
}
