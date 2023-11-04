<?php

namespace Modules\Shipping\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Shipping\Models\Shipping;
use Modules\User\Models\User;

class ShippingPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('shipping-list');
    }

    public function view(User $user, Shipping $shipping): bool
    {
        return $user->can('shipping-list');
    }

    public function create(User $user): bool
    {
        return $user->can('shipping-create');
    }

    public function update(User $user, Shipping $shipping): bool
    {
        return $user->can('shipping-update');
    }

    public function delete(User $user, Shipping $shipping): bool
    {
        return $user->can('shipping-delete');
    }
}