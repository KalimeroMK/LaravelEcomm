<?php

declare(strict_types=1);

namespace Modules\Google2fa\Actions;

use Modules\Google2fa\Models\Google2fa;
use PragmaRX\Google2FAQRCode\Google2FA as PragmaRXGoogle2FA;

readonly class Verify2FAAction
{
    public function execute(Google2fa $loginSecurity, string $verificationCode): bool
    {
        $google2fa = new PragmaRXGoogle2FA;

        return $google2fa->verifyKey($loginSecurity->google2fa_secret, $verificationCode);
    }
}
