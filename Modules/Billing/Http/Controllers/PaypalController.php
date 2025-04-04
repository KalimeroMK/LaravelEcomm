<?php

declare(strict_types=1);

namespace Modules\Billing\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Modules\Billing\Service\PaypalService;
use Modules\Core\Helpers\Payment;
use Modules\Core\Http\Controllers\Api\CoreController;

class PaypalController extends CoreController
{
    private Payment $payment;

    private PaypalService $paypal_service;

    public function __construct(Payment $payment, PaypalService $paypal_service)
    {
        $this->payment = $payment;
        $this->paypal_service = $paypal_service;
    }

    /**
     * Initiates a charge through PayPal.
     *
     * @param  Request  $request  The incoming request.
     * @return Response|string Either returns a redirect response or an error message.
     */
    public function charge(Request $request)
    {
        try {
            $response = $this->paypal_service->get_gateway()->purchase([
                'amount' => $this->payment->calculate($request),
                'currency' => config('paypal.currency'),
                'returnUrl' => route('payment.success'),
                'cancelUrl' => route('payment.cancel'),
            ])->send();

            if ($response->isRedirect()) {
                // Get the redirect response object provided by Omnipay
                return $response->getRedirectResponse();
            }

            // Returns a string message on failure
            return $response->getMessage();

        } catch (Exception $e) {
            // Returns a string message on exception
            return $e->getMessage();
        }
    }

    public function success(Request $request): ?string
    {
        return $this->paypal_service->completePurchase($request);
    }

    /**
     * @return string]
     */
    public function error(): string
    {
        return 'User cancelled the payment.';
    }
}
