<?php

declare(strict_types=1);

namespace Modules\User\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\User\Models\User;
use Modules\User\Models\UserAddress;

class UserAddressPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any addresses.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the address.
     */
    public function view(User $user, UserAddress $address): bool
    {
        return $user->id === $address->user_id;
    }

    /**
     * Determine whether the user can create addresses.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the address.
     */
    public function update(User $user, UserAddress $address): bool
    {
        return $user->id === $address->user_id;
    }

    /**
     * Determine whether the user can delete the address.
     */
    public function delete(User $user, UserAddress $address): bool
    {
        return $user->id === $address->user_id;
    }

    /**
     * Determine whether the user can restore the address.
     */
    public function restore(User $user, UserAddress $address): bool
    {
        return $user->id === $address->user_id;
    }

    /**
     * Determine whether the user can permanently delete the address.
     */
    public function forceDelete(User $user, UserAddress $address): bool
    {
        return $user->id === $address->user_id;
    }
}
