<?php

declare(strict_types=1);

namespace Modules\Google2fa\Actions;

use Modules\Google2fa\Models\Google2fa;

readonly class VerifyRecoveryCodeAction
{
    public function execute(Google2fa $loginSecurity, string $code): bool
    {
        if (! $loginSecurity->hasRecoveryCode($code)) {
            return false;
        }

        $loginSecurity->removeRecoveryCode($code);

        return true;
    }
}
