<?php

namespace Modules\Brand\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Brand\Models\Brand;
use Modules\User\Models\User;

class BrandPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('brand-list');
    }

    public function view(User $user, Brand $brand): bool
    {
        return $user->can('brand-list');
    }

    public function create(User $user): bool
    {
        return $user->can('brand-create');
    }

    public function update(User $user, Brand $brand): bool
    {
        return $user->can('brand-update');
    }

    public function delete(User $user, Brand $brand): bool
    {
        return $user->can('brand-delete');
    }
}
