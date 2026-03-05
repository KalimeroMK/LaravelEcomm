<?php

declare(strict_types=1);

namespace Modules\Billing\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Response;
use Modules\Billing\Actions\Paypal\CreatePaypalChargeAction;
use Modules\Billing\DTOs\PaypalDTO;
use Modules\Core\Helpers\Helper;
use Modules\Core\Helpers\Payment;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Order\Actions\StoreOrderAction;
use Modules\Order\DTOs\OrderDTO;

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

    public function success(Request $request, StoreOrderAction $storeOrderAction): RedirectResponse|string
    {
        $paymentId = $request->input('paymentId');
        $pendingOrder = session('pending_order');
        
        if (!$pendingOrder) {
            return redirect()->route('front.index')->with('error', 'No pending order found.');
        }
        
        try {
            // Get cart items before clearing
            $userId = $pendingOrder['user_id'] ?? '';
            $cartItems = Helper::getAllProductFromCart($userId);
            
            if ($cartItems->isEmpty()) {
                return redirect()->route('front.cart')->with('error', 'Your cart is empty.');
            }
            
            // Update order data with PayPal payment info
            $pendingOrder['payment_status'] = 'paid';
            $pendingOrder['transaction_reference'] = $paymentId;
            
            // Create the order
            $dto = OrderDTO::fromArray($pendingOrder);
            $order = $storeOrderAction->execute($dto);
            
            // Associate cart items with the order
            foreach ($cartItems as $cartItem) {
                $cartItem->update(['order_id' => $order->id]);
            }
            
            // Save address to user's address book if logged in
            if (!empty($pendingOrder['user_id']) && !empty($pendingOrder['save_address'])) {
                $user = \Modules\User\Models\User::find($pendingOrder['user_id']);
                if ($user) {
                    $user->addresses()->create([
                        'type' => 'shipping',
                        'is_default' => !empty($pendingOrder['make_default_address']),
                        'first_name' => $pendingOrder['first_name'],
                        'last_name' => $pendingOrder['last_name'],
                        'email' => $pendingOrder['email'],
                        'phone' => $pendingOrder['phone'],
                        'country' => $pendingOrder['country'],
                        'city' => $pendingOrder['city'],
                        'address1' => $pendingOrder['address1'],
                        'address2' => $pendingOrder['address2'] ?? null,
                        'post_code' => $pendingOrder['post_code'],
                    ]);
                }
            }
            
            // Clear session data
            session()->forget('cart');
            session()->forget('coupon');
            session()->forget('pending_order');
            
            return redirect()->route('front.index')->with('success', 'Payment successful! Order number: ' . $order->order_number);
        } catch (Exception $e) {
            return redirect()->route('front.index')->with('error', 'Payment succeeded but order creation failed. Please contact support. Error: ' . $e->getMessage());
        }
    }

    public function cancel(): RedirectResponse
    {
        return redirect()->route('front.checkout')->with('error', 'Payment was cancelled.');
    }
}
