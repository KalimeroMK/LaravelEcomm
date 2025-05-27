<?php

declare(strict_types=1);

namespace Modules\Bundle\Models\Policy;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Bundle\Models\Bundle;
use Modules\User\Models\User;

class BundlePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('bundle-list');
    }

    public function view(User $user, Bundle $bundle): bool
    {
        return $user->can('bundle-show');
    }

    public function create(User $user): bool
    {
        return $user->can('bundle-create');
    }

    public function update(User $user, Bundle $bundle): bool
    {
        return $user->can('bundle-update');
    }

    public function delete(User $user, Bundle $bundle): bool
    {
        return $user->can('bundle-delete');
    }

    public function restore(User $user, Bundle $bundle): bool
    {
        return $user->can('bundle-restore');
    }

    public function forceDelete(User $user, Bundle $bundle): bool
    {
        return $user->can('bundle-force-delete');
    }
}
