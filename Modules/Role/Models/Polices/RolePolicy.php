<?php

declare(strict_types=1);

namespace Modules\Role\Models\Polices;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Role\Models\Role;
use Modules\User\Models\User;

class RolePolicy
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

    public function update(User $user, Role $role): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function delete(User $user, Role $role): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }
}
