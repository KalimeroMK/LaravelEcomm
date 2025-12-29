<?php

declare(strict_types=1);

namespace Modules\Google2fa\Actions;

use Modules\Google2fa\Models\Google2fa;
use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;
use PragmaRX\Google2FAQRCode\Google2FA as PragmaRXGoogle2FA;

readonly class Enable2FAAction
{
    public function __construct(
        private GenerateRecoveryCodesAction $generateRecoveryCodesAction
    ) {}

    /**
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws InvalidCharactersException
     * @throws SecretKeyTooShortException
     */
    public function execute(Google2fa $loginSecurity, string $verificationCode): bool
    {
        $google2fa = new PragmaRXGoogle2FA;

        $valid = $google2fa->verifyKey($loginSecurity->google2fa_secret, $verificationCode);

        if ($valid) {
            $loginSecurity->google2fa_enable = true;
            $loginSecurity->save();

            // Generate recovery codes when enabling 2FA
            $this->generateRecoveryCodesAction->execute($loginSecurity);

            return true;
        }

        return false;
    }
}
