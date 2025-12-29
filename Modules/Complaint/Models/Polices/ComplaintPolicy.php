<?php

declare(strict_types=1);

namespace Modules\Complaint\Models\Polices;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Complaint\Models\Complaint;
use Modules\User\Models\User;

class ComplaintPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        // Сите корисници можат да гледаат complaints, но ќе се филтрираат во action
        return true;
    }

    public function view(User $user, Complaint $complaint): bool
    {
        // Admin и super-admin можат да гледаат сите complaints
        if ($user->hasAnyRole(['admin', 'super-admin'])) {
            return true;
        }

        // Обични корисници можат да гледаат само свои complaints
        return $user->id === $complaint->user_id;
    }

    public function create(User $user): bool
    {
        // Сите автентификувани корисници можат да креираат complaints
        return true;
    }

    public function update(User $user, Complaint $complaint): bool
    {
        // Admin и super-admin можат да ажурираат сите complaints
        if ($user->hasAnyRole(['admin', 'super-admin'])) {
            return true;
        }

        // Обични корисници можат да ажурираат само свои complaints
        return $user->id === $complaint->user_id;
    }

    public function delete(User $user, Complaint $complaint): bool
    {
        // Admin и super-admin можат да бришат сите complaints
        if ($user->hasAnyRole(['admin', 'super-admin'])) {
            return true;
        }

        // Обични корисници можат да бришат само свои complaints
        return $user->id === $complaint->user_id;
    }
}
