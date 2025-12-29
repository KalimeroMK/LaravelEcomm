<?php

declare(strict_types=1);

namespace Modules\Google2fa\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Google2fa\Actions\Disable2FAAction;
use Modules\Google2fa\Actions\Enable2FAAction;
use Modules\Google2fa\Actions\Generate2FASecretAction;
use Modules\Google2fa\Actions\GenerateRecoveryCodesAction;
use Modules\Google2fa\Actions\Get2FAQRCodeAction;
use Modules\Google2fa\Actions\Verify2FAAction;
use Modules\Google2fa\Actions\VerifyRecoveryCodeAction;
use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;

class Google2faController extends CoreController
{
    public function __construct(
        private readonly Generate2FASecretAction $generate2FASecretAction,
        private readonly Get2FAQRCodeAction $get2FAQRCodeAction,
        private readonly Enable2FAAction $enable2FAAction,
        private readonly Disable2FAAction $disable2FAAction,
        private readonly Verify2FAAction $verify2FAAction,
        private readonly GenerateRecoveryCodesAction $generateRecoveryCodesAction,
        private readonly VerifyRecoveryCodeAction $verifyRecoveryCodeAction
    ) {}

    /**
     * Get 2FA status and QR code if secret exists.
     */
    public function status(): JsonResponse
    {
        $user = auth()->user();
        $loginSecurity = $user->loginSecurity;

        if (! $loginSecurity || ! $loginSecurity->exists()) {
            return $this
                ->setMessage('2FA is not set up.')
                ->respond([
                    'enabled' => false,
                    'has_secret' => false,
                ]);
        }

        $response = [
            'enabled' => (bool) $loginSecurity->google2fa_enable,
            'has_secret' => ! empty($loginSecurity->google2fa_secret),
        ];

        if ($loginSecurity->google2fa_enable && $loginSecurity->google2fa_secret) {
            $qrData = $this->get2FAQRCodeAction->execute($loginSecurity, $user->email);
            $response['qr_code'] = $qrData['qr_code'];
            $response['secret_key'] = $qrData['secret_key'];
        }

        return $this
            ->setMessage('2FA status retrieved successfully.')
            ->respond($response);
    }

    /**
     * Generate 2FA secret key.
     *
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws InvalidCharactersException
     * @throws SecretKeyTooShortException
     */
    public function generateSecret(): JsonResponse
    {
        $user = auth()->user();
        $loginSecurity = $this->generate2FASecretAction->execute($user);

        $qrData = $this->get2FAQRCodeAction->execute($loginSecurity, $user->email);

        return $this
            ->setMessage('Secret key generated successfully.')
            ->respond([
                'secret_key' => $qrData['secret_key'],
                'qr_code' => $qrData['qr_code'],
            ]);
    }

    /**
     * Enable 2FA.
     *
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws InvalidCharactersException
     * @throws SecretKeyTooShortException
     */
    public function enable(Request $request): JsonResponse
    {
        $request->validate([
            'verification_code' => 'required|string|size:6',
        ]);

        $user = auth()->user();
        $loginSecurity = $user->loginSecurity;

        if (! $loginSecurity || ! $loginSecurity->google2fa_secret) {
            return $this
                ->setCode(400)
                ->setMessage('Please generate a secret key first.')
                ->respond(null);
        }

        $enabled = $this->enable2FAAction->execute($loginSecurity, $request->input('verification_code'));

        if (! $enabled) {
            return $this
                ->setCode(422)
                ->setMessage('Invalid verification code. Please try again.')
                ->respond(null);
        }

        return $this
            ->setMessage('2FA is enabled successfully.')
            ->respond([
                'enabled' => true,
                'recovery_codes' => $loginSecurity->fresh()->recovery_codes,
            ]);
    }

    /**
     * Disable 2FA.
     */
    public function disable(): JsonResponse
    {
        $user = auth()->user();
        $this->disable2FAAction->execute($user);

        return $this
            ->setMessage('2FA is now disabled.')
            ->respond(['enabled' => false]);
    }

    /**
     * Verify 2FA code.
     */
    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'verification_code' => 'required|string|size:6',
        ]);

        $user = auth()->user();
        $loginSecurity = $user->loginSecurity;

        if (! $loginSecurity || ! $loginSecurity->google2fa_secret) {
            return $this
                ->setCode(400)
                ->setMessage('2FA is not set up.')
                ->respond(null);
        }

        $valid = $this->verify2FAAction->execute($loginSecurity, $request->input('verification_code'));

        if (! $valid) {
            return $this
                ->setCode(422)
                ->setMessage('Invalid verification code.')
                ->respond(null);
        }

        // Mark 2FA as verified for this session
        session(['2fa_verified' => true]);

        return $this
            ->setMessage('2FA verified successfully.')
            ->respond(['verified' => true]);
    }

    /**
     * Get recovery codes.
     */
    public function recoveryCodes(): JsonResponse
    {
        $user = auth()->user();
        $loginSecurity = $user->loginSecurity;

        if (! $loginSecurity || ! $loginSecurity->google2fa_enable) {
            return $this
                ->setCode(400)
                ->setMessage('2FA is not enabled.')
                ->respond(null);
        }

        return $this
            ->setMessage('Recovery codes retrieved successfully.')
            ->respond([
                'recovery_codes' => $loginSecurity->recovery_codes ?? [],
            ]);
    }

    /**
     * Regenerate recovery codes.
     */
    public function regenerateRecoveryCodes(): JsonResponse
    {
        $user = auth()->user();
        $loginSecurity = $user->loginSecurity;

        if (! $loginSecurity || ! $loginSecurity->google2fa_enable) {
            return $this
                ->setCode(400)
                ->setMessage('2FA is not enabled.')
                ->respond(null);
        }

        $codes = $this->generateRecoveryCodesAction->execute($loginSecurity);

        return $this
            ->setMessage('Recovery codes regenerated successfully.')
            ->respond(['recovery_codes' => $codes]);
    }

    /**
     * Verify recovery code.
     */
    public function verifyRecoveryCode(Request $request): JsonResponse
    {
        $request->validate([
            'recovery_code' => 'required|string',
        ]);

        $user = auth()->user();
        $loginSecurity = $user->loginSecurity;

        if (! $loginSecurity) {
            return $this
                ->setMessage('2FA is not enabled.')
                ->setStatusCode(400)
                ->respond(null);
        }

        $code = mb_strtoupper(str_replace(' ', '', $request->input('recovery_code')));

        if ($this->verifyRecoveryCodeAction->execute($loginSecurity, $code)) {
            // Mark 2FA as verified for this session
            session(['2fa_verified' => true]);

            return $this
                ->setMessage('Recovery code verified successfully.')
                ->respond(['verified' => true]);
        }

        return $this
            ->setCode(422)
            ->setMessage('Invalid recovery code.')
            ->respond(null);
    }
}
