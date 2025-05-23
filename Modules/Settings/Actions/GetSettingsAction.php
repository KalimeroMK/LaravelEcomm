<?php

declare(strict_types=1);

namespace Modules\Settings\Actions;

use Modules\Settings\DTOs\SettingsDTO;
use Modules\Settings\Models\Setting;

class GetSettingsAction
{
    public function execute(): SettingsDTO
    {
        $settings = Setting::all();

        return new SettingsDTO($settings);
    }
}
