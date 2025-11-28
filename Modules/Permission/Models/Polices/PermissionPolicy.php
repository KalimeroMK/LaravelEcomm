<?php

declare(strict_types=1);

namespace Modules\Permission\Models\Polices;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Permission\Models\Permission;
use Modules\User\Models\User;

class PermissionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function view(User $user, Permission $permission): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function update(User $user, Permission $permission): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function delete(User $user, Permission $permission): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function restore(User $user, Permission $permission): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function forceDelete(User $user, Permission $permission): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }
}
