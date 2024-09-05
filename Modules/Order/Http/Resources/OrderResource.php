<?php

namespace Modules\Order\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Cart\Http\Resources\CartResource;
use Modules\Order\Models\Order;
use Modules\Shipping\Http\Resources\ShippingResource;
use Modules\User\Http\Resource\UserResource;

/** @mixin Order */
class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'sub_total' => $this->sub_total,
            'total_amount' => $this->total_amount,
            'quantity' => $this->quantity,
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'cart_info_count' => $this->cart_info_count,
            'carts_count' => $this->carts_count,
            'user' => UserResource::make($this->whenLoaded('user')),
            'shipping' => new ShippingResource($this->whenLoaded('shipping')),
            'carts' => CartResource::collection($this->whenLoaded('carts')),
        ];
    }
}
