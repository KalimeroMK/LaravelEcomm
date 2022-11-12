<?php

namespace Modules\Product\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Product\Models\Product;
use Modules\User\Models\User;

class ProductPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(User $user): bool
    {
        return $user->can('product-list');
    }
    
    public function view(User $user, Product $product): bool
    {
        return $user->can('product-list');
    }
    
    public function create(User $user): bool
    {
        return $user->can('product-create');
    }
    
    public function update(User $user, Product $product): bool
    {
        return $user->can('product-update');
    }
    
    public function delete(User $user, Product $product): bool
    {
        return $user->can('product-delete');
    }
    
    public function restore(User $user, Product $product): bool
    {
    }
    
    public function forceDelete(User $user, Product $product): bool
    {
    }
}