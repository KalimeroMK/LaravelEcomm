<?php

namespace Modules\User\Observers;

use Modules\User\Models\User;

class UserObserver
{
    public function creating(User $user): void
    {
        $user->assignRole('client');
    }
    
    public function updating(User $user): void
    {
        //
    }
    
    public function deleted(User $user)
    {
        //
    }
    
    public function restored(User $user)
    {
        //
    }
    
    public function forceDeleted(User $user)
    {
        //
    }
}
