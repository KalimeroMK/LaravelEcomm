<?php

declare(strict_types=1);

namespace Modules\Google2fa\Actions;

use Modules\Google2fa\Models\Google2faSetting;

readonly class Get2FASettingsAction
{
    public function execute(): Google2faSetting
    {
        return Google2faSetting::getSettings();
    }
}
