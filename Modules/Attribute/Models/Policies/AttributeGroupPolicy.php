<?php

declare(strict_types=1);

namespace Modules\Attribute\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Attribute\Models\AttributeGroup;
use Modules\User\Models\User;

class AttributeGroupPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('attribute-list');
    }

    public function view(User $user, AttributeGroup $attribute): bool
    {
        return $user->can('attribute-list');
    }

    public function create(User $user): bool
    {
        return $user->can('attribute-create');
    }

    public function update(User $user, AttributeGroup $attribute): bool
    {
        return $user->can('attribute-update');
    }

    public function delete(User $user, AttributeGroup $attribute): bool
    {
        return $user->can('attribute-delete');
    }
}
