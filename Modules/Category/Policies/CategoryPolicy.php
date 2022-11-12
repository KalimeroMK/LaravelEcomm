<?php

namespace Modules\Category\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Category\Models\Category;
use Modules\User\Models\User;

class CategoryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(User $user): bool
    {
        return $user->can('categories-list');
    }
    
    public function view(User $user, Category $category): bool
    {
        return $user->can('categories-list');
    }
    
    public function create(User $user): bool
    {
        return $user->can('categories-create');
    }
    
    public function update(User $user, Category $category): bool
    {
        return $user->can('categories-create');
    }
    
    public function delete(User $user, Category $category): bool
    {
        return $user->can('categories-delete');
    }
    
    public function restore(User $user, Category $category): bool
    {
    }
    
    public function forceDelete(User $user, Category $category): bool
    {
    }
}