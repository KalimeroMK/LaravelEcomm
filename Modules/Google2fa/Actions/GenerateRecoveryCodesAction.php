<?php

declare(strict_types=1);

namespace Modules\Google2fa\Actions;

use Illuminate\Support\Str;
use Modules\Google2fa\Models\Google2fa;
use Modules\Google2fa\Models\Google2faSetting;

readonly class GenerateRecoveryCodesAction
{
    public function execute(Google2fa $loginSecurity): array
    {
        $settings = Google2faSetting::getSettings();
        $count = $settings->recovery_codes_count;

        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = mb_strtoupper(Str::random(8)).'-'.mb_strtoupper(Str::random(8));
        }

        $loginSecurity->recovery_codes = $codes;
        $loginSecurity->save();

        return $codes;
    }
}
