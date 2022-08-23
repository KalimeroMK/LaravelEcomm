<?php

namespace Modules\Core\Helpers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Modules\Billing\Http\Requests\Api\Stripe;
use Modules\Core\Notifications\StatusNotification;
use Modules\Order\Models\Order;
use Modules\Shipping\Models\Shipping;
use Modules\User\Models\User;

class Payment
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  Stripe  $request
     *
     * @return array|RedirectResponse
     */
    public function calculate(Stripe $request): array|RedirectResponse
    {
        $order                      = new Order();
        $order_data                 = $request->all();
        $order_data['order_number'] = 'ORD-'.strtoupper(Str::random(10));
        $order_data['user_id']      = $request->user()->id;
        $order_data['shipping_id']  = $request->shipping;
        $shipping                   = Shipping::where('id', $order_data['shipping_id'])->pluck('price');
        $order_data['sub_total']    = Helper::totalCartPrice();
        $order_data['quantity']     = Helper::cartCount();
        if (session('coupon')) {
            $order_data['coupon'] = session('coupon')['value'];
        }
        if ($request->shipping) {
            if (session('coupon')) {
                $order_data['total_amount'] = Helper::totalCartPrice() + $shipping[0] - session('coupon')['value'];
            } else {
                $order_data['total_amount'] = Helper::totalCartPrice() + $shipping[0];
            }
        } else {
            if (session('coupon')) {
                $order_data['total_amount'] = Helper::totalCartPrice() - session('coupon')['value'];
            } else {
                $order_data['total_amount'] = Helper::totalCartPrice();
            }
        }
        $order_data['status'] = "new";
        if (request('payment_method') == 'paypal') {
            $order_data['payment_method'] = 'paypal';
            $order_data['payment_status'] = 'paid';
        } elseif (request('payment_method') == 'stripe') {
            $order_data['payment_method'] = 'stripe';
            $order_data['payment_status'] = 'paid';
        } else {
            $order_data['payment_method'] = 'cod';
            $order_data['payment_status'] = 'Unpaid';
        }
        $order->fill($order_data);
        $order->save();
        $details = [
            'title'     => 'New order created',
            'actionURL' => route('user.order.show', $order->id),
            'fas'       => 'fa-file-alt',
        ];
        $id      = $order->id;
        $total   = $order_data['total_amount'];
        Notification::send(User::role('super-admin')->get(), new StatusNotification($details));
        
        return [$id, $total];
    }
}
