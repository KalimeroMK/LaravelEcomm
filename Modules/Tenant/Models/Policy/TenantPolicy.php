<?php

namespace Modules\Tenant\Models\Policy;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Tenant\Models\Tenant;
use Modules\User\Models\User;

class TenantPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('tenant-list');

    }

    public function view(User $user, Tenant $tenant): bool
    {
        return $user->can('tenant-list');

    }

    public function create(User $user): bool
    {
        return $user->can('tenant-create');

    }

    public function update(User $user, Tenant $tenant): bool
    {
        return $user->can('tenant-update');

    }

    public function delete(User $user, Tenant $tenant): bool
    {
        return $user->can('tenant-delete');

    }

}
