<?php

namespace Modules\Complaint\Models\Polices;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Complaint\Models\Complaint;
use Modules\User\Models\User;

class ComplaintPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('complaint-list');
    }

    public function view(User $user, Complaint $complaint): bool
    {
        return $user->hasPermissionTo('complaint-list');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('complaint-create');
    }

    public function update(User $user, Complaint $complaint): bool
    {
        return $user->hasPermissionTo('complaint-edit');
    }

    public function delete(User $user, Complaint $complaint): bool
    {
        return $user->hasPermissionTo('complaint-delete');
    }
    
}
