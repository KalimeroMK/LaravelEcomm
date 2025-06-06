<?php

declare(strict_types=1);

namespace Modules\Attribute\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Attribute\Models\Attribute;
use Modules\User\Models\User;

class AttributePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('attribute-group-list');
    }

    public function view(User $user, Attribute $attribute): bool
    {
        return $user->can('attribute-group-list');
    }

    public function create(User $user): bool
    {
        return $user->can('attribute-group-create');
    }

    public function update(User $user, Attribute $attribute): bool
    {
        return $user->can('attribute-group-update');
    }

    public function delete(User $user, Attribute $attribute): bool
    {
        return $user->can('attribute-group-delete');
    }
}
