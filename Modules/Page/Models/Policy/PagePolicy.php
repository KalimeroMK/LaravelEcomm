<?php

namespace Modules\Page\Models\Policy;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Page\Models\Page;
use Modules\User\Models\User;

class PagePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('page-list');

    }

    public function view(User $user, Page $page): bool
    {
        return $user->can('page-list');

    }

    public function create(User $user): bool
    {
        return $user->can('page-create');

    }

    public function update(User $user, Page $page): bool
    {
        return $user->can('page-edit');

    }

    public function delete(User $user, Page $page): bool
    {
        return $user->can('page-delete');
    }
}
