<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Modules\Coupon\Actions\ApplyCouponAction;
use Modules\Core\Helpers\Helper;
use Modules\Order\Actions\StoreOrderAction;
use Modules\Order\DTOs\OrderDTO;
use Modules\Order\Http\Requests\Store as OrderStoreRequest;
use Modules\Shipping\Repository\ShippingRepository;

class ProcessCheckoutAction
{
    public function __construct(
        private readonly StoreOrderAction $storeOrderAction,
        private readonly ApplyCouponAction $applyCouponAction,
        private readonly ShippingRepository $shippingRepository,
    ) {}

    public function execute(OrderStoreRequest $request): RedirectResponse
    {
        $user      = Auth::user();
        $userId    = (string) ($user?->id ?? '');
        $cartItems = Helper::getAllProductFromCart($userId);
        $subtotal  = Helper::totalCartPrice($userId);
        $quantity  = $cartItems->sum('quantity');

        if ($cartItems->isEmpty()) {
            return redirect()->back()->with('error', 'Your cart is empty.');
        }

        // Shipping
        $shippingId   = null;
        $shippingCost = 0;

        if (Helper::cartRequiresShipping($userId)) {
            $shippingId = $request->input('shipping');
            if ($shippingId) {
                $shipping     = $this->shippingRepository->find((int) $shippingId);
                $shippingCost = $shipping?->price ?? 0;
            }
        }

        // Coupon
        $couponData    = session('coupon');
        $couponDiscount = $couponData['discount'] ?? 0;
        $couponId       = $couponData['id'] ?? null;

        if (($couponData['free_shipping'] ?? false) && $couponDiscount === 0) {
            $couponDiscount = $shippingCost;
        }

        $totalAmount = max(0, $subtotal + $shippingCost - $couponDiscount);
        $paymentMethod = $request->input('payment_method', 'cod');

        $orderData = [
            'user_id'        => $user?->id,
            'sub_total'      => $subtotal,
            'shipping_id'    => $shippingId,
            'total_amount'   => $totalAmount,
            'quantity'       => $quantity,
            'payment_method' => $paymentMethod,
            'payment_status' => 'pending',
            'status'         => 'pending',
            'first_name'     => $request->input('first_name'),
            'last_name'      => $request->input('last_name'),
            'email'          => $request->input('email'),
            'phone'          => $request->input('phone'),
            'country'        => $request->input('country'),
            'city'           => $request->input('city'),
            'address1'       => $request->input('address1'),
            'address2'       => $request->input('address2'),
            'post_code'      => $request->input('post_code'),
        ];

        // Payment gateway redirects — store order data in session and redirect
        if ($paymentMethod === 'paypal') {
            session()->put('pending_order', $orderData);

            return redirect()->route('payment');
        }

        if ($paymentMethod === 'stripe') {
            session()->put('pending_order', $orderData);

            return redirect()->route('stripe', Auth::id());
        }

        // COD — create order immediately
        $order = $this->storeOrderAction->execute(OrderDTO::fromArray($orderData));

        foreach ($cartItems as $cartItem) {
            $cartItem->update(['order_id' => $order->id]);
        }

        if ($user && $request->has('save_address')) {
            $user->addresses()->create([
                'type'       => 'shipping',
                'is_default' => $request->has('make_default_address'),
                'first_name' => $request->input('first_name'),
                'last_name'  => $request->input('last_name'),
                'email'      => $request->input('email'),
                'phone'      => $request->input('phone'),
                'country'    => $request->input('country'),
                'city'       => $request->input('city'),
                'address1'   => $request->input('address1'),
                'address2'   => $request->input('address2'),
                'post_code'  => $request->input('post_code'),
            ]);
        }

        if ($couponId) {
            $this->applyCouponAction->recordUsage(
                $couponId,
                $order->id,
                $user?->id,
                session()->getId(),
                $couponDiscount
            );
        }

        session()->forget(['cart', 'coupon', 'pending_order']);

        return redirect()->route('front.index')->with(
            'success',
            'Order placed successfully! Order number: '.$order->order_number
        );
    }
}
