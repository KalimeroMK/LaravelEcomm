<?php

namespace Modules\Category\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Category\Models\Category;
use Modules\User\Models\User;

class CategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('category-list');
    }

    public function view(User $user): bool
    {
        return $user->can('category-list');
    }

    public function create(User $user): bool
    {
        return $user->can('category-create');
    }

    public function update(User $user, Category $category): bool
    {
        return $user->can('category-create');
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->can('category-delete');
    }
}
