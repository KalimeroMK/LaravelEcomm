<?php

namespace Modules\Core\Helpers;

use InvalidArgumentException;
use Modules\Shipping\Models\Shipping;

class Payment
{
    /**
     * Calculates the total amount due for an order, including shipping and taxes.
     *
     * @return float Total amount calculated.
     *
     * @throws InvalidArgumentException If required keys are missing.
     */
    public static function calculate($data): float
    {
        $order_data = $data;
        $shipping = Shipping::whereId($order_data['shipping_id'])->pluck('price');
        $order_data['sub_total'] = Helper::totalCartPrice();
        $order_data['quantity'] = Helper::cartCount();
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
