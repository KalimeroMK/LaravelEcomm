<?php

declare(strict_types=1);

namespace Modules\Cart\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Cart\Models\Cart;
use Modules\User\Models\User;

class CartPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true; // Any authenticated user can view their own cart
    }

    public function view(User $user, Cart $cart): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']) || $user->id === $cart->user_id;
    }

    public function create(User $user): bool
    {
        return true; // Any authenticated user can create cart items
    }

    public function update(User $user, Cart $cart): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']) || $user->id === $cart->user_id;
    }

    public function delete(User $user, Cart $cart): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']) || $user->id === $cart->user_id;
    }
}
