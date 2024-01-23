<?php

namespace Modules\Message\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Message\Models\Message;
use Modules\User\Models\User;

class MessagePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('message-list');
    }

    public function view(User $user, Message $message): bool
    {
        return $user->can('message-list');
    }

    public function create(User $user): bool
    {
        return $user->can('message-create');
    }

    public function update(User $user, Message $message): bool
    {
        return $user->can('message-update');
    }

    public function delete(User $user, Message $message): bool
    {
        return $user->can('message-delete');
    }

    public function restore(User $user, Message $message): bool
    {
    }

    public function forceDelete(User $user, Message $message): bool
    {
    }
}