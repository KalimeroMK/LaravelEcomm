<?php

declare(strict_types=1);

namespace Modules\Billing\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Billing\Actions\Stripe\CreateStripeChargeAction;
use Modules\Billing\DTOs\StripeDTO;
use Modules\Core\Helpers\Helper;
use Modules\Core\Helpers\Payment;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Order\Actions\StoreOrderAction;
use Modules\Order\DTOs\OrderDTO;
use Session;

// theme_view() is loaded via composer autoload files

class StripeController extends CoreController
{
    private CreateStripeChargeAction $createAction;

    private Payment $payment;

    public function __construct(CreateStripeChargeAction $createAction, Payment $payment)
    {
        $this->createAction = $createAction;
        $this->payment = $payment;
    }

    /**
     * success response method.
     */
    public function stripe(int $id): View|Factory|Application
    {
        return view(theme_view('pages.stripe'), ['id' => $id]);
    }

    public function stripePost(Request $request, StoreOrderAction $storeOrderAction): RedirectResponse
    {
        $pendingOrder = session('pending_order');
        
        if (!$pendingOrder) {
            return redirect()->route('front.checkout')->with('error', 'No pending order found. Please try again.');
        }
        
        try {
            // Process Stripe payment
            $dto = new StripeDTO(
                amount: $this->payment->calculate($request),
                currency: 'usd',
                source: $request->stripeToken,
                description: 'Order from ' . ($pendingOrder['email'] ?? 'Guest')
            );
            $paymentResult = $this->createAction->execute($dto);
            
            // Get cart items before clearing
            $userId = $pendingOrder['user_id'] ?? '';
            $cartItems = Helper::getAllProductFromCart($userId);
            
            if ($cartItems->isEmpty()) {
                return redirect()->route('front.cart')->with('error', 'Your cart is empty.');
            }
            
            // Update order data with Stripe payment info
            $pendingOrder['payment_status'] = 'paid';
            $pendingOrder['transaction_reference'] = $request->stripeToken;
            
            // Create the order
            $orderDto = OrderDTO::fromArray($pendingOrder);
            $order = $storeOrderAction->execute($orderDto);
            
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
            
            Session::flash('success', 'Payment successful! Order number: ' . $order->order_number);
            return redirect()->route('orders.index');
        } catch (\Exception $e) {
            return redirect()->route('front.checkout')->with('error', 'Payment failed: ' . $e->getMessage());
        }
    }
}
