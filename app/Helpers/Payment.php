<?php

namespace App\Helpers;

use App\Notifications\StatusNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Modules\Cart\Models\Cart;
use Modules\Order\Models\Order;
use Modules\Shipping\Models\Shipping;
use Modules\User\Models\User;

class Payment
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     *
     * @return array|RedirectResponse
     */
    public function calculate(Request $request): array|RedirectResponse
    {
        request()->validate(
            [
                'first_name' => 'string|required',
                'last_name'  => 'string|required',
                'address1'   => 'string|required',
                'address2'   => 'string|nullable',
                'coupon'     => 'nullable|numeric',
                'phone'      => 'numeric|required',
                'post_code'  => 'string|nullable',
                'email'      => 'string|required',
            ]
        );
        if (empty(Cart::whereUserId(auth()->user()->id)->where('order_id', null)->first())) {
            request()->session()->flash('error', 'Cart is Empty !');
            
            return back();
        }
        
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
