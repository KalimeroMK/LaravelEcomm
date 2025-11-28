<?php

declare(strict_types=1);

namespace Modules\Brand\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Brand\Models\Brand;
use Modules\User\Models\User;

class BrandPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function view(User $user, Brand $brand): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function update(User $user, Brand $brand): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function delete(User $user, Brand $brand): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }
}
