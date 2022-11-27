<?php

namespace Modules\Billing\Service;

use Illuminate\Http\Request;
use Modules\Billing\Http\Controllers\PaypalController;
use Modules\Billing\Http\Traits\Order;
use Modules\Core\Helpers\Helper;

class PaypalService
{
    use Order;
    
    private PaypalController $paypal_controller;
    
    public function __construct(PaypalController $paypal_controller)
    {
        $this->paypal_controller = $paypal_controller;
    }
    
    /**
     * Charge a payment and store the transaction.
     *
     * @param  Request  $request
     *
     * @return string|null
     */
    public function success(Request $request): ?string
    {
        // Once the transaction has been approved, we need to complete it.
        if ($request->input('paymentId') && $request->input('PayerID')) {
            $transaction = $this->paypal_controller->get_gateway()->completePurchase([
                'payer_id'             => $request->input('PayerID'),
                'transactionReference' => $request->input('paymentId'),
            ]);
            $response    = $transaction->send();
            
            if ($response->isSuccessful()) {
                // The customer has successfully paid.
                $order_data = $response->getData();
                $this->orderSave(Helper::totalCartPrice());
                
                return "Payment is successful. Your transaction id is: " . $order_data['id'];
            } else {
                return $response->getMessage();
            }
        } else {
            return 'Transaction is declined';
        }
    }
}