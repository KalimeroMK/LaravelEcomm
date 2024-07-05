<?php

namespace Modules\Tenant\Models\Policy;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Tenant\Models\Tenant;
use Modules\User\Models\User;

class TenantPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool {}

    public function view(User $user, Tenant $tenant): bool {}

    public function create(User $user): bool {}

    public function update(User $user, Tenant $tenant): bool {}

    public function delete(User $user, Tenant $tenant): bool {}

    public function restore(User $user, Tenant $tenant): bool {}

    public function forceDelete(User $user, Tenant $tenant): bool {}
}
