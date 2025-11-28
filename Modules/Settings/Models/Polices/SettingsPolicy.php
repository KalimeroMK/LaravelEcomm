<?php

declare(strict_types=1);

namespace Modules\Settings\Models\Polices;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Settings\Models\Setting;
use Modules\User\Models\User;

class SettingsPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function view(User $user, Setting $setting): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function update(User $user, Setting $setting): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function delete(User $user, Setting $setting): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }
}
