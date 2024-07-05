<?php

namespace Modules\Cart\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Cart\Models\Cart;
use Modules\User\Models\User;

class CartPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('cart-list');
    }

    public function view(User $user, Cart $cart): bool
    {
        return $user->can('cart-list');
    }

    public function create(User $user): bool
    {
        return $user->can('cart-create');
    }

    public function update(User $user, Cart $cart): bool
    {
        return $user->can('cart-create') && $user->id == $cart->user_id;
    }

    public function delete(User $user, Cart $cart): bool
    {
        return $user->can('cart-delete') && $user->id == $cart->user_id;
    }
}
