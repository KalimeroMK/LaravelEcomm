<?php

declare(strict_types=1);

namespace Modules\Google2fa\Actions;

use Modules\Google2fa\Models\Google2fa;
use Modules\User\Models\User;
use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;
use PragmaRX\Google2FAQRCode\Google2FA as PragmaRXGoogle2FA;

readonly class Generate2FASecretAction
{
    /**
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws InvalidCharactersException
     * @throws SecretKeyTooShortException
     */
    public function execute(User $user): Google2fa
    {
        $google2fa = new PragmaRXGoogle2FA;

        $login_security = Google2fa::firstOrNew(['user_id' => $user->id]);
        $login_security->user_id = $user->id;
        $login_security->google2fa_enable = false;
        $login_security->google2fa_secret = $google2fa->generateSecretKey();
        $login_security->save();

        return $login_security;
    }
}
