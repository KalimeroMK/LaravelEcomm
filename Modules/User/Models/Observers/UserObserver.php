<?php

namespace Modules\User\Models\Observers;

use Modules\User\Models\User;

class UserObserver
{
    public function creating(User $user): void
    {
        if ($user->email != 'superadmin@mail.com') {
            $user->assignRole('client');
        }
    }
}
