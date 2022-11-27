<?php

namespace Modules\Core\Helpers;

use Modules\Shipping\Models\Shipping;

class Payment
{
    /**
     * @param $data
     *
     * @return float
     */
    public static function calculate($data): float
    {
        $order_data              = $data;
        $shipping                = Shipping::whereId($order_data['shipping_id'])->pluck('price');
        $order_data['sub_total'] = Helper::totalCartPrice();
        $order_data['quantity']  = Helper::cartCount();
        if (session('coupon')) {
            $order_data['coupon'] = session('coupon')['value'];
        }
        if ($data->shipping) {
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
        
        return $order_data['total_amount'];
    }
}
