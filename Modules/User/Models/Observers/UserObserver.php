<?php

namespace Modules\User\Models\Observers;

use Illuminate\Support\Facades\Hash;
use Modules\User\Models\User;

class UserObserver
{
    public function creating(User $user): void
    {
        if ($user->email != 'superadmin@mail.com') {
            $user->assignRole('client');
        }
        $user->password = Hash::make($user->password);
    }

    public function updating(User $user): void
    {
        if ($user->isDirty('password')) {
            $user->password = Hash::make($user->password);
        }
    }
}
