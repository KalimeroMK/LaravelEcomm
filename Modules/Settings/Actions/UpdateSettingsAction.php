<?php

declare(strict_types=1);

namespace Modules\Settings\Actions;

use Modules\Settings\Models\Setting;

class UpdateSettingsAction
{
    public function execute(int $id, array $data): void
    {
        $setting = Setting::findOrFail($id);
        $setting->update($data);
    }
}
