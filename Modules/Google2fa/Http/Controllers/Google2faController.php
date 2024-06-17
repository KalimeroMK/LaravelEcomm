<?php

namespace Modules\Google2fa\Http\Controllers;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Modules\Google2fa\Models\Google2fa;
use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;
use PragmaRX\Google2FAQRCode\Google2FA as PragmaRXGoogle2FA;

class Google2faController extends Controller
{


    /**
     * @return Application|Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|RedirectResponse|View
     */
    public function show2faForm()
    {
        $user = Auth::user();

        if ($user->loginSecurity()->exists()) {
            $google2fa = (new PragmaRXGoogle2FA());
            $google2fa_url = $google2fa->getQRCodeUrl(
                'Kalimero-Ecomm',
                $user->email,
                $user->loginSecurity->google2fa_secret
            );
            $renderer = new ImageRenderer(
                new RendererStyle(400),
                new SvgImageBackEnd()
            );

            $writer = new Writer($renderer);
            $qrImage = $writer->writeString($google2fa_url);
            $google2fa_url = 'data:image/svg+xml;base64,'.base64_encode($qrImage);
            $secret_key = $user->loginSecurity->google2fa_secret;
            return view('google2fa::2fa_settings', compact($user, $secret_key, $google2fa_url));
        }
        return redirect()->route('user-profile')->with('error', "Pls enable 2FA");
    }

    /**
     * Generate 2FA secret key
     * @return RedirectResponse
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws InvalidCharactersException
     * @throws SecretKeyTooShortException
     */
    public function generate2faSecret()
    {
        $user = Auth::user();
        // Initialise the 2FA class
        $google2fa = (new PragmaRXGoogle2FA());

        // Add the secret key to the registration data
        $login_security = Google2fa::firstOrNew(['user_id' => $user->id]);
        $login_security->user_id = $user->id;
        $login_security->google2fa_enable = false;
        $login_security->google2fa_secret = $google2fa->generateSecretKey();
        $login_security->save();
        return redirect()->route('2fa')->with('success', "Secret key is generated.");
    }

    /**
     * Enable 2FA
     * @param  Request  $request
     * @return RedirectResponse
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws InvalidCharactersException
     * @throws SecretKeyTooShortException
     */
    public function enable2fa(Request $request)
    {
        $user = Auth::user();
        $google2fa = (new PragmaRXGoogle2FA());

        $secret = $request->input('secret');
        $valid = $google2fa->verifyKey($user->loginSecurity->google2fa_secret, $secret);
        if ($valid) {
            $user->loginSecurity->google2fa_enable = 1;
            $user->loginSecurity->save();
            return redirect()->route('2fa')->with('success', "2FA is enabled successfully.");
        } else {
            return redirect()->route('2fa')->with('error', "Invalid verification Code, Please try again.");
        }
    }

    /**
     * Disable 2FA
     * @return RedirectResponse
     */
    public function disable2fa()
    {
        $user = Auth::user();
        $user->loginSecurity->google2fa_enable = 0;
        $user->loginSecurity->save();
        return redirect()->back()->with('success', "2FA is now disabled.");
    }

    /**
     * @return Application|RedirectResponse|Redirector
     */
    public function verify2fa()
    {
        return redirect(URL()->previous());
    }
}
