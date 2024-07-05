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

    }

    public function view(User $user, Page $page): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, Page $page): bool
    {
    }

    public function delete(User $user, Page $page): bool
    {
    }

    public function restore(User $user, Page $page): bool
    {
    }

    public function forceDelete(User $user, Page $page): bool
    {
    }
}
