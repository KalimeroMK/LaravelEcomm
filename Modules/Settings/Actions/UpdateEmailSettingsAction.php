<?php

declare(strict_types=1);

namespace Modules\Settings\Actions;

use Modules\Settings\Models\Setting;

readonly class UpdateEmailSettingsAction
{
    public function execute(Setting $setting, array $data): Setting
    {
        $emailSettings = array_merge($setting->email_settings ?? [], $data);
        $setting->update(['email_settings' => $emailSettings]);

        return $setting->fresh();
    }
}
