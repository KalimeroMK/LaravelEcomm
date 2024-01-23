<?php

namespace Modules\Bundle\Models\Policy;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Bundle\Models\Bundle;
use Modules\User\Models\User;

class BundlePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'user']);
    }

    public function view(User $user, Bundle $bundle): bool
    {
        return $user->hasAnyRole(['super-admin', 'user']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'user']);
    }

    public function update(User $user, Bundle $bundle): bool
    {
        return $user->hasAnyRole(['super-admin', 'user']);
    }

    public function delete(User $user, Bundle $bundle): bool
    {
        return $user->hasAnyRole(['super-admin', 'user']);
    }

    public function restore(User $user, Bundle $bundle): bool
    {
        return $user->hasAnyRole(['super-admin', 'user']);
    }

    public function forceDelete(User $user, Bundle $bundle): bool
    {
        return $user->hasAnyRole(['super-admin', 'user']);
    }
}
