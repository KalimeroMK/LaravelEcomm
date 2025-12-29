<?php

declare(strict_types=1);

namespace Modules\Google2fa\Support;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Google2fa\Actions\Enforce2FAAction;
use PragmaRX\Google2FALaravel\Exceptions\InvalidSecretKey;
use PragmaRX\Google2FALaravel\Support\Authenticator;

class Google2FAAuthenticator extends Authenticator
{
    /**
     * Boot the Google 2FA Authenticator.
     *
     * @param  Request  $request
     * @return $this
     */
    public function boot($request): static
    {
        parent::boot($request);

        return $this;
    }

    /**
     * Check if the user is authenticated with 2FA.
     */
    public function isAuthenticated(): bool
    {
        return $this->isAuthenticated();
    }

    /**
     * Generate the response when the user is not authenticated with 2FA.
     */
    public function makeRequestOneTimePasswordResponse(): Response|RedirectResponse
    {
        return $this->makeRequestOneTimePasswordResponse();
    }

    /**
     * Determine if the user can pass without checking the OTP.
     */
    protected function canPassWithoutCheckingOTP(): bool
    {
        $user = $this->getUser();

        if (! $user) {
            return true;
        }

        // Check if 2FA is enforced for this user
        $enforceAction = app(Enforce2FAAction::class);
        if ($enforceAction->execute($user)) {
            // 2FA is enforced, user must have it enabled
            if ($user->loginSecurity === null || ! $user->loginSecurity->google2fa_enable) {
                return false; // Cannot pass without 2FA if enforced
            }
        }

        if ($user->loginSecurity === null) {
            return true;
        }

        return
            ! $user->loginSecurity->google2fa_enable ||
            ! $this->isEnabled() ||
            $this->noUserIsAuthenticated() ||
            $this->twoFactorAuthStillValid();
    }

    /**
     * Get the Google 2FA secret key.
     *
     * @throws InvalidSecretKey
     */
    protected function getGoogle2FASecretKey(): string
    {
        $secret = $this->getUser()->loginSecurity->{$this->config('otp_secret_column')};

        if (empty($secret)) {
            throw new InvalidSecretKey('Secret key cannot be empty.');
        }

        return $secret;
    }
}
