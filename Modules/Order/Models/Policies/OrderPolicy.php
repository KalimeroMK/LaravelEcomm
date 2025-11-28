<?php

declare(strict_types=1);

namespace Modules\Order\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Order\Models\Order;
use Modules\User\Models\User;

class OrderPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true; // Any authenticated user can view their own orders
    }

    public function view(User $user, Order $order): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']) || $user->id === $order->user_id;
    }

    public function create(User $user): bool
    {
        return true; // Any authenticated user can create orders
    }

    public function update(User $user, Order $order): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']) || $user->id === $order->user_id;
    }

    public function delete(User $user, Order $order): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']) || $user->id === $order->user_id;
    }
}
