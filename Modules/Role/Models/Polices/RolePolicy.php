<?php

namespace Modules\Role\Models\Polices;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Role\Models\Role;
use Modules\User\Models\User;

class RolePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('role-list');
    }

    public function view(User $user): bool
    {
        return $user->can('role-list');
    }

    public function create(User $user): bool
    {
        return $user->can('role-create');
    }

    public function update(User $user, Role $role): bool
    {
        return $user->can('role-update');
    }

    public function delete(User $user, Role $role): bool
    {
        return $user->can('role-delete');
    }

}
