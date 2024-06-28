<?php

namespace Modules\Coupon\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Coupon\Models\Coupon;
use Modules\User\Models\User;

class CouponPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('coupon-list');
    }

    public function view(User $user, Coupon $coupon): bool
    {
        return $user->can('coupon-list');
    }

    public function create(User $user): bool
    {
        return $user->can('coupon-create');
    }

    public function update(User $user, Coupon $coupon): bool
    {
        return $user->can('coupon-update');
    }

    public function delete(User $user, Coupon $coupon): bool
    {
        return $user->can('coupon-delete');
    }
}