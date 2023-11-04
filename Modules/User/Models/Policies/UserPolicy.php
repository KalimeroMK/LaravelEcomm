<?php

namespace Modules\User\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\User\Models\User;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('user-list');
    }

    public function view(User $user): bool
    {
        return $user->can('user-list');
    }

    public function create(User $user): bool
    {
        return $user->can('user-create');
    }

    public function update(User $user, User $model): bool
    {
        return $user->can('user-update');
    }

    public function delete(User $user, User $model): bool
    {
        return $user->can('user-delete');
    }
}
