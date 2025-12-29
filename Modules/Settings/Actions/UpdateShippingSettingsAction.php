<?php

declare(strict_types=1);

namespace Modules\Settings\Actions;

use Modules\Settings\Models\Setting;

readonly class UpdateShippingSettingsAction
{
    public function execute(Setting $setting, array $data): Setting
    {
        $shippingSettings = array_merge($setting->shipping_settings ?? [], $data);
        $setting->update(['shipping_settings' => $shippingSettings]);

        return $setting->fresh();
    }
}
