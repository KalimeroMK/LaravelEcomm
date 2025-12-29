<?php

declare(strict_types=1);

namespace Modules\Google2fa\Actions;

use Modules\Google2fa\Models\Google2faSetting;
use Modules\User\Models\User;

readonly class Enforce2FAAction
{
    public function execute(User $user): bool
    {
        $settings = Google2faSetting::getSettings();

        // Check if 2FA is enforced for admins
        if ($settings->enforce_for_admins && $user->hasAnyRole(['admin', 'super-admin'])) {
            return true;
        }

        // Check if 2FA is enforced for all users
        if ($settings->enforce_for_users) {
            return true;
        }

        // Check if 2FA is enforced for specific roles
        if ($settings->enforced_roles) {
            foreach ($settings->enforced_roles as $role) {
                if ($user->hasRole($role)) {
                    return true;
                }
            }
        }

        return false;
    }
}
