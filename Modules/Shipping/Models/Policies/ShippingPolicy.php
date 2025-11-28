<?php

declare(strict_types=1);

namespace Modules\Shipping\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Shipping\Models\Shipping;
use Modules\User\Models\User;

class ShippingPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function view(User $user, Shipping $shipping): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function update(User $user, Shipping $shipping): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function delete(User $user, Shipping $shipping): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }
}
