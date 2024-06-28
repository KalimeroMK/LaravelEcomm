<?php

namespace Modules\Cart\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Billing\Http\Resources\WishlistResource;
use Modules\Cart\Models\Cart;
use Modules\Order\Http\Resources\OrderResource;
use Modules\Product\Http\Resources\ProductResource;

/** @mixin Cart */
class CartResource extends JsonResource
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string> Array of field rules.
     */

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'price' => $this->price,
            'status' => $this->status,
            'quantity' => $this->quantity,
            'amount' => $this->amount,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'wishlists_count' => $this->wishlists_count,

            'product_id' => $this->product_id,
            'order_id' => $this->order_id,
            'user_id' => $this->user_id,

            'order' => new OrderResource($this->whenLoaded('order')),
            'product' => new ProductResource($this->whenLoaded('product')),
            'wishlists' => WishlistResource::collection($this->whenLoaded('wishlists')),

        ];
    }
}
