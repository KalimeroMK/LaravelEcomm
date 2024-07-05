<?php

namespace Modules\Order\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Order\Models\Order;
use Modules\User\Models\User;

class OrderPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('order-list');
    }

    public function view(User $user, Order $order): bool
    {
        return $user->can('order-list');
    }

    public function create(User $user): bool
    {
        return $user->can('order-create');
    }

    public function update(User $user, Order $order): bool
    {
        return $user->can('order-list') && $user->id == $order->user_id;
    }

    public function delete(User $user, Order $order): bool
    {
        return $user->can('order-delete') && $user->id == $order->user_id;
    }
}
