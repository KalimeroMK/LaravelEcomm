<?php

declare(strict_types=1);

namespace Modules\Settings\Actions;

use Modules\Settings\Models\Setting;

readonly class UpdatePaymentSettingsAction
{
    public function execute(Setting $setting, array $data): Setting
    {
        $paymentSettings = array_merge($setting->payment_settings ?? [], $data);
        $setting->update(['payment_settings' => $paymentSettings]);

        return $setting->fresh();
    }
}
