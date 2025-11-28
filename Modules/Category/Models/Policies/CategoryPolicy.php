<?php

declare(strict_types=1);

namespace Modules\Category\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Category\Models\Category;
use Modules\User\Models\User;

class CategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function view(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function update(User $user, Category $category): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }
}
