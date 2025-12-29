<?php

declare(strict_types=1);

namespace Modules\Google2fa\Actions;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Modules\Google2fa\Models\Google2fa;
use PragmaRX\Google2FAQRCode\Google2FA as PragmaRXGoogle2FA;

readonly class Get2FAQRCodeAction
{
    public function execute(Google2fa $loginSecurity, string $userEmail, string $appName = 'Kalimero-Ecomm'): array
    {
        $google2fa = new PragmaRXGoogle2FA;

        $google2fa_url = $google2fa->getQRCodeUrl(
            $appName,
            $userEmail,
            $loginSecurity->google2fa_secret
        );

        $renderer = new ImageRenderer(
            new RendererStyle(400),
            new SvgImageBackEnd
        );

        $writer = new Writer($renderer);
        $qrImage = $writer->writeString($google2fa_url);
        $qrCodeImage = 'data:image/svg+xml;base64,'.base64_encode($qrImage);

        return [
            'qr_code' => $qrCodeImage,
            'secret_key' => $loginSecurity->google2fa_secret,
            'url' => $google2fa_url,
        ];
    }
}
