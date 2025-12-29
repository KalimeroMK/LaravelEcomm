<?php

declare(strict_types=1);

namespace Modules\Google2fa\Actions;

use Modules\Google2fa\Models\Google2faSetting;

readonly class Update2FASettingsAction
{
    public function execute(array $data): Google2faSetting
    {
        $settings = Google2faSetting::getSettings();

        $settings->update([
            'enforce_for_admins' => $data['enforce_for_admins'] ?? false,
            'enforce_for_users' => $data['enforce_for_users'] ?? false,
            'enforced_roles' => $data['enforced_roles'] ?? [],
            'recovery_codes_count' => $data['recovery_codes_count'] ?? 10,
            'require_backup_codes' => $data['require_backup_codes'] ?? false,
        ]);

        return $settings->fresh();
    }
}
