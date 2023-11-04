<?php

namespace Modules\Settings\Models\Polices;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Settings\Models\Setting;
use Modules\User\Models\User;

class SettingsPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('settings-list');
    }

    public function view(User $user, Setting $setting): bool
    {
        return $user->can('settings-list');
    }

    public function create(User $user): bool
    {
        return $user->can('settings-list');
    }

    public function update(User $user, Setting $setting): bool
    {
        return $user->can('settings-list');
    }

    public function delete(User $user, Setting $setting): bool
    {
        return $user->can('settings-list');
    }

}
