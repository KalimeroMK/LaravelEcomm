<?php

declare(strict_types=1);

namespace Modules\Billing\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Modules\Billing\Actions\Paypal\CreatePaypalChargeAction;
use Modules\Billing\DTOs\PaypalDTO;
use Modules\Core\Helpers\Payment;
use Modules\Core\Http\Controllers\Api\CoreController;

class PaypalController extends CoreController
{
    private Payment $payment;

    private CreatePaypalChargeAction $createAction;

    public function __construct(Payment $payment, CreatePaypalChargeAction $createAction)
    {
        $this->payment = $payment;
        $this->createAction = $createAction;
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
            $dto = new PaypalDTO(
                amount: $this->payment->calculate($request),
                currency: config('paypal.currency'),
                returnUrl: route('payment.success'),
                cancelUrl: route('payment.cancel')
            );
            $response = $this->createAction->execute($dto);

            if ($response->isRedirect()) {
                return $response->getRedirectResponse();
            }

            return $response->getMessage();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function success(Request $request): ?string
    {
        // You may want to move orderSave logic here if needed
        // $this->orderSave(Helper::totalCartPrice());
        return 'Payment is successful. Your transaction id is: '.$request->input('paymentId');
    }

    public function error(): string
    {
        return 'User cancelled the payment.';
    }
}
