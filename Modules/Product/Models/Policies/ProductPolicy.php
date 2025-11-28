<?php

declare(strict_types=1);

namespace Modules\Product\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Product\Models\Product;
use Modules\User\Models\User;

class ProductPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function view(User $user): bool
    {
        return true; // Anyone can view products
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function update(User $user, Product $product): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }
}
