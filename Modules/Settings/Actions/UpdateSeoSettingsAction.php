<?php

declare(strict_types=1);

namespace Modules\Settings\Actions;

use Modules\Settings\Models\Setting;

readonly class UpdateSeoSettingsAction
{
    public function execute(Setting $setting, array $data): Setting
    {
        $seoSettings = array_merge($setting->seo_settings ?? [], $data);
        $setting->update(['seo_settings' => $seoSettings]);

        return $setting->fresh();
    }
}
