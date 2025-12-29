<?php

declare(strict_types=1);

namespace Modules\Google2fa\Models\Policies;

use Modules\Google2fa\Models\Google2faSetting;
use Modules\User\Models\User;

class Google2faSettingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function view(User $user, Google2faSetting $setting): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function update(User $user, Google2faSetting $setting): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }
}
