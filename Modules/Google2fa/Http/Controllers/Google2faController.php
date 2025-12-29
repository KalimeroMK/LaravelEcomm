<?php

declare(strict_types=1);

namespace Modules\Google2fa\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
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

class Google2faController extends Controller
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
     * @return Application|Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|RedirectResponse|View
     */
    public function show2faForm()
    {
        $user = Auth::user();

        if ($user->loginSecurity()->exists()) {
            $qrData = $this->get2FAQRCodeAction->execute($user->loginSecurity, $user->email);
            $secret_key = $qrData['secret_key'];
            $google2fa_url = $qrData['qr_code'];

            return view('google2fa::2fa_settings', compact('user', 'secret_key', 'google2fa_url'));
        }

        return redirect()->route('user-profile')->with('error', 'Pls enable 2FA');
    }

    /**
     * Generate 2FA secret key
     *
     * @return RedirectResponse
     *
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws InvalidCharactersException
     * @throws SecretKeyTooShortException
     */
    public function generate2faSecret()
    {
        $user = Auth::user();
        $this->generate2FASecretAction->execute($user);

        return redirect()->route('admin.2fa')->with('success', 'Secret key is generated.');
    }

    /**
     * Enable 2FA
     *
     * @return RedirectResponse
     *
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws InvalidCharactersException
     * @throws SecretKeyTooShortException
     */
    public function enable2fa(Request $request)
    {
        $user = Auth::user();
        $enabled = $this->enable2FAAction->execute($user->loginSecurity, $request->input('secret'));

        if ($enabled) {
            return redirect()->route('admin.2fa')->with('success', '2FA is enabled successfully.');
        }

        return redirect()->route('admin.2fa')->with('error', 'Invalid verification Code, Please try again.');
    }

    /**
     * Disable 2FA
     *
     * @return RedirectResponse
     */
    public function disable2fa()
    {
        $user = Auth::user();
        $this->disable2FAAction->execute($user);

        return redirect()->back()->with('success', '2FA is now disabled.');
    }

    /**
     * @return Application|RedirectResponse|Redirector
     */
    public function verify2fa(): Redirector|RedirectResponse
    {
        return redirect(URL()->previous());
    }

    /**
     * Show recovery codes
     */
    public function showRecoveryCodes(): View|Factory|Application
    {
        $user = Auth::user();
        $loginSecurity = $user->loginSecurity;

        if (! $loginSecurity || ! $loginSecurity->google2fa_enable) {
            return redirect()->route('admin.2fa')->with('error', '2FA is not enabled.');
        }

        return view('google2fa::recovery_codes', [
            'recovery_codes' => $loginSecurity->recovery_codes ?? [],
        ]);
    }

    /**
     * Regenerate recovery codes
     */
    public function regenerateRecoveryCodes(): RedirectResponse
    {
        $user = Auth::user();
        $loginSecurity = $user->loginSecurity;

        if (! $loginSecurity || ! $loginSecurity->google2fa_enable) {
            return redirect()->route('admin.2fa')->with('error', '2FA is not enabled.');
        }

        $codes = $this->generateRecoveryCodesAction->execute($loginSecurity);

        return redirect()->route('admin.2fa.recovery-codes')->with('success', 'Recovery codes regenerated successfully.');
    }

    /**
     * Verify recovery code during login
     */
    public function verifyRecoveryCode(Request $request): RedirectResponse
    {
        $request->validate([
            'recovery_code' => 'required|string',
        ]);

        $user = Auth::user();
        $loginSecurity = $user->loginSecurity;

        if (! $loginSecurity) {
            return redirect()->back()->with('error', '2FA is not enabled.');
        }

        $code = mb_strtoupper(str_replace(' ', '', $request->input('recovery_code')));

        if ($this->verifyRecoveryCodeAction->execute($loginSecurity, $code)) {
            // Mark 2FA as verified for this session
            session(['2fa_verified' => true]);

            return redirect()->intended('/')->with('success', 'Recovery code verified successfully.');
        }

        return redirect()->back()->with('error', 'Invalid recovery code.');
    }
}
