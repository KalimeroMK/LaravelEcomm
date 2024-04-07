<?php

namespace Modules\Billing\Service;

use Illuminate\Http\Request;
use Modules\Billing\Http\Traits\Order;
use Modules\Core\Helpers\Helper;
use Omnipay\Common\GatewayInterface;
use Omnipay\Omnipay;

class PaypalService
{
    use Order;

    private GatewayInterface $gateway;

    public function __construct()
    {
        $this->gateway = Omnipay::create('PayPal_Rest');
        $this->gateway->setClientId(config('paypal.sandbox.client_id'));
        $this->gateway->setSecret(config('paypal.sandbox.client_secret'));
        $this->gateway->setTestMode(true); // set it to 'false' when going live
    }

    public function completePurchase(Request $request): ?string
    {
        if ($request->input('paymentId') && $request->input('PayerID')) {
            $transaction = $this->gateway->completePurchase([
                'payer_id' => $request->input('PayerID'),
                'transactionReference' => $request->input('paymentId'),
            ]);
            $response = $transaction->send();

            if ($response->isSuccessful()) {
                $order_data = $response->getData();
                $this->orderSave(Helper::totalCartPrice());

                return "Payment is successful. Your transaction id is: ".$order_data['id'];
            } else {
                return $response->getMessage();
            }
        } else {
            return 'Transaction is declined';
        }
    }

    /**
     * Get the configured PayPal gateway instance.
     *
     * @return GatewayInterface
     */
    public function get_gateway(): GatewayInterface
    {
        return $this->gateway;
    }
}
