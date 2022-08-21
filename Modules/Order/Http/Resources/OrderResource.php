<?php

namespace Modules\Order\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Order\Models\Order;

/** @mixin Order */
class OrderResource extends JsonResource
{
    /**
     * @param  Request  $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id'              => $this->id,
            'order_number'    => $this->order_number,
            'sub_total'       => $this->sub_total,
            'coupon'          => $this->coupon,
            'total_amount'    => $this->total_amount,
            'quantity'        => $this->quantity,
            'payment_method'  => $this->payment_method,
            'payment_status'  => $this->payment_status,
            'status'          => $this->status,
            'first_name'      => $this->first_name,
            'last_name'       => $this->last_name,
            'email'           => $this->email,
            'phone'           => $this->phone,
            'country'         => $this->country,
            'post_code'       => $this->post_code,
            'address1'        => $this->address1,
            'address2'        => $this->address2,
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at,
            'cart_info_count' => $this->cart_info_count,
            'carts_count'     => $this->carts_count,
            
            'user_id'     => $this->user_id,
            'shipping_id' => $this->shipping_id,
        ];
    }
}
