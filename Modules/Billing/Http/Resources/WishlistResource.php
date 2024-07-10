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
     * Transform the resource into an array.
     *
     * @return array<string, mixed> Array of various types depending on the property.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'amount' => $this->amount,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'product_id' => $this->product_id,
            'cart_id' => $this->cart_id,
            'user_id' => $this->user_id,
            'product' => new ProductResource($this->whenLoaded('product')),
        ];
    }
}
