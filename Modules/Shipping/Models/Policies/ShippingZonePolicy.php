<?php

declare(strict_types=1);

namespace Modules\Shipping\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Shipping\Models\ShippingZone;
use Modules\User\Models\User;

class ShippingZonePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function view(User $user, ShippingZone $zone): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function update(User $user, ShippingZone $zone): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function delete(User $user, ShippingZone $zone): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }
}
