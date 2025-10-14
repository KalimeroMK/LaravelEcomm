<?php

declare(strict_types=1);

namespace Modules\Message\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Message\Models\Message;
use Modules\User\Models\User;

class MessagePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function view(User $user, Message $message): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function update(User $user, Message $message): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function delete(User $user, Message $message): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function restore(User $user, Message $message): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function forceDelete(User $user, Message $message): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }
}
