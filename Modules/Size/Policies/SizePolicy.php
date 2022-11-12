<?php

namespace Modules\Size\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Size\Models\Size;
use Modules\User\Models\User;

class SizePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(User $user): bool
    {
        return $user->can('size-list');
    }
    
    public function view(User $user, Size $size): bool
    {
        return $user->can('size-list');
    }
    
    public function create(User $user): bool
    {
        return $user->can('size-create');
    }
    
    public function update(User $user, Size $size): bool
    {
        return $user->can('size-update');
    }
    
    public function delete(User $user, Size $size): bool
    {
        return $user->can('size-delete');
    }
    
    public function restore(User $user, Size $size): bool
    {
    }
    
    public function forceDelete(User $user, Size $size): bool
    {
    }
}