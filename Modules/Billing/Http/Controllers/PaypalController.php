<?php

namespace Modules\Billing\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Modules\Billing\Service\PaypalService;
use Modules\Core\Helpers\Payment;
use Modules\Core\Http\Controllers\Api\CoreController;
use Throwable;

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
     * @throws Throwable
     */
    public function charge(Request $request)
    {
        try {
            $response = $this->paypal_service->get_gateway()->purchase([
                'amount'    => $this->payment->calculate($request),
                'currency'  => config('paypal.currency'),
                'returnUrl' => route('payment.success'),
                'cancelUrl' => route('payment.cancel'),
            ])->send();

            if ($response->isRedirect()) {
                $response->redirect();
            } else {
                return $response->getMessage();
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function success(Request $request)
    {
        return $this->paypal_service->completePurchase($request);
    }

    public function error()
    {
        return 'User cancelled the payment.';
    }
}
