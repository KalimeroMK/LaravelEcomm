<?php

namespace Modules\Google2fa\Support;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
     *
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        return $this->isAuthenticated();
    }

    /**
     * Generate the response when the user is not authenticated with 2FA.
     *
     * @return Response|RedirectResponse
     */
    public function makeRequestOneTimePasswordResponse(): Response|RedirectResponse
    {
        return $this->makeRequestOneTimePasswordResponse();
    }

    /**
     * Determine if the user can pass without checking the OTP.
     *
     * @return bool
     */
    protected function canPassWithoutCheckingOTP(): bool
    {
        if ($this->getUser()->loginSecurity == null) {
            return true;
        }

        return
            !$this->getUser()->loginSecurity->google2fa_enable ||
            !$this->isEnabled() ||
            $this->noUserIsAuthenticated() ||
            $this->twoFactorAuthStillValid();
    }

    /**
     * Get the Google 2FA secret key.
     *
     * @return string
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
