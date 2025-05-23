<?php

declare(strict_types=1);

namespace Modules\Billing\Actions\Paypal;

use Modules\Billing\DTOs\PaypalDTO;
use Omnipay\Omnipay;

readonly class CreatePaypalChargeAction
{
    public function execute(PaypalDTO $dto): \Omnipay\Common\Message\ResponseInterface
    {
        $gateway = Omnipay::create('PayPal_Rest');
        $gateway->setClientId(config('paypal.sandbox.client_id'));
        $gateway->setSecret(config('paypal.sandbox.client_secret'));
        $gateway->setTestMode(true);

        $response = $gateway->purchase([
            'amount' => $dto->amount,
            'currency' => $dto->currency,
            'returnUrl' => $dto->returnUrl,
            'cancelUrl' => $dto->cancelUrl,
        ])->send();

        return $response;
    }
}
