<?php

namespace Modules\Billing\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Billing\Models\Wishlist;
use Modules\Product\Http\Resources\ProductResource;

/** @mixin Wishlist */
class WishlistResource extends JsonResource
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
            'quantity' => $this->quantity,
            'amount' => $this->amount,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'product_id' => $this->product_id,
            'cart_id' => $this->cart_id,
            'user_id' => $this->user_id,

            'product' => new ProductResource($this->whenLoaded('product')),
        ];
    }
}
