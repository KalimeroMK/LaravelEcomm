<?php

namespace Modules\User\Observers;

use Modules\User\Models\User;

class UserObserver
{
    public function creating(User $user): void
    {
        $user->assignRole('client');
    }
}
