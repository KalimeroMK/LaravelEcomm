<?php

declare(strict_types=1);

namespace Modules\Coupon\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Coupon\Models\Coupon;
use Modules\User\Models\User;

class CouponPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function view(User $user, Coupon $coupon): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function update(User $user, Coupon $coupon): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function delete(User $user, Coupon $coupon): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }
}
