<?php

namespace Modules\Billing\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Modules\Billing\Service\PaypalService;
use Modules\Core\Helpers\Payment;
use Modules\Core\Http\Controllers\Api\CoreController;
use Omnipay\Common\GatewayInterface;
use Omnipay\Omnipay;
use Throwable;

class PaypalController extends CoreController
{
    private GatewayInterface $gateway;
    private Payment $payment;
    private PaypalService $paypal_service;
    
    public function __construct(Payment $payment, PaypalService $paypal_service)
    {
        $this->payment = $payment;
        $this->gateway = Omnipay::create('PayPal_Rest');
        $this->gateway->setClientId(config('paypal.sandbox.client_id'));
        $this->gateway->setSecret(config('paypal.sandbox.client_secret'));
        $this->gateway->setTestMode(true); //set it to 'false' when go live
        $this->paypal_service = $paypal_service;
    }
    
    /**
     * Initiate a payment on PayPal.
     *
     * @param  Request  $request
     *
     * @return string|void|null
     */
    public function charge(Request $request)
    {
        try {
            $response = $this->gateway->purchase([
                'amount'    => $this->payment->calculate($request),
                'currency'  => config('paypal.currency'),
                'returnUrl' => route('payment.success'),
                'cancelUrl' => route('payment.cancel'),
            ])->send();
            
            if ($response->isRedirect()) {
                $response->redirect(); // this will automatically forward the customer
            } else {
                // not successful
                return $response->getMessage();
            }
        } catch (Exception $e) {
            return $e->getMessage();
        } catch (Throwable $e) {
        }
    }
    
    /**
     * Charge a payment and store the transaction.
     *
     * @param  Request  $request
     *
     * @return string|null
     */
    public function success(Request $request)
    {
        return $this->paypal_service->success($request);
    }
    
    /**
     * Error Handling.
     */
    public function error()
    {
        return 'User cancelled the payment.';
    }
    
    /**
     * @return GatewayInterface
     */
    public function get_gateway(): GatewayInterface
    {
        return $this->gateway;
    }
}