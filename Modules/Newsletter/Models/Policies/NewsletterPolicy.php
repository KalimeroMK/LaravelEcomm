<?php

namespace Modules\Newsletter\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Newsletter\Models\Newsletter;
use Modules\User\Models\User;

class NewsletterPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('newsletter-list');
    }

    public function view(User $user, Newsletter $newsletter): bool
    {
        return $user->can('newsletter-list');
    }

    public function create(User $user): bool
    {
        return $user->can('newsletter-create');
    }

    public function update(User $user, Newsletter $newsletter): bool
    {
        return $user->can('newsletter-edit');
    }

    public function delete(User $user, Newsletter $newsletter): bool
    {
        return $user->can('newsletter-delete');
    }
}
