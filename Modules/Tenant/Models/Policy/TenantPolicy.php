<?php

declare(strict_types=1);

namespace Modules\Tenant\Models\Policy;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Tenant\Models\Tenant;
use Modules\User\Models\User;

class TenantPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function view(User $user, Tenant $tenant): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function update(User $user, Tenant $tenant): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function delete(User $user, Tenant $tenant): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }
}
