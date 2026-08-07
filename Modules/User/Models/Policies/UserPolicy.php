<?php

declare(strict_types=1);

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

    public function view(User $user, ?User $model = null): bool
    {
        // Allow users to view their own profile, or if they have user-list permission
        if ($model && $user->id === $model->id) {
            return true;
        }

        return $user->can('user-list');
    }

    public function create(User $user): bool
    {
        return $user->can('user-create');
    }

    public function update(User $user, User $model): bool
    {
        // Allow users to update their own profile, or if they have user-update permission.
        // NOTE: this grants profile-field access only. Changing a user's roles is a
        // separate ability - see assignRoles() - so self-update can never escalate.
        if ($user->id === $model->id) {
            return true;
        }

        return $user->can('user-update');
    }

    /**
     * Determine whether the user may change the role set of an account.
     *
     * This is deliberately NOT implied by update(): update() lets a user edit their
     * own profile, and if role syncing rode along with it any authenticated user
     * could grant themselves super-admin.
     *
     * Self-assignment is always denied here. Super-admins are unaffected because
     * Gate::before() in PolicyServiceProvider short-circuits every ability for them.
     */
    public function assignRoles(User $user, ?User $model = null): bool
    {
        if ($model instanceof User && $user->id === $model->id) {
            return false;
        }

        return $user->can('user-update');
    }

    public function delete(User $user, User $model): bool
    {
        return $user->can('user-delete');
    }
}
