<?php

namespace Modules\Billing\Http\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Modules\Cart\Models\Cart;
use Modules\Core\Helpers\Helper;
use Modules\Core\Notifications\StatusNotification;
use Modules\User\Models\User;

trait Order
{
    public function orderSave($amount): void
    {
        $order = new \Modules\Order\Models\Order();
        Cart::where('user_id', Auth::id())->where('order_id', null)->update(['order_id' => $order->id]);
        $order_data['order_number']   = 'ORD-' . strtoupper(Str::random(10));
        $order_data['user_id']        = Auth::id();
        $order_data['sub_total']      = Helper::totalCartPrice();
        $order_data['quantity']       = Helper::cartCount();
        $order_data['status']         = "new";
        $order_data['total_amount']   = $amount;
        $order_data['payment_method'] = 'stripe';
        $order_data['payment_status'] = 'paid';
        $order->fill($order_data);
        $order->save();
        $details = [
            'title'     => 'New order created',
            'actionURL' => route('order.show', $order->id),
            'fas'       => 'fa-file-alt',
        ];
        
        Notification::send(User::role('super-admin')->get(), new StatusNotification($details));
    }
    
}
