<?php

declare(strict_types=1);

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
     * Transform the resource into an array.
     *
     * @return array<string, mixed> // Mixed indicates that the array can contain multiple data types
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'price' => $this->price,
            'status' => $this->status,
            'quantity' => $this->quantity,
            'amount' => $this->amount,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'wishlists_count' => $this->when(
                $this->relationLoaded('wishlists'),
                fn() => $this->wishlists->count(),
                0
            ),

            'product_id' => $this->product_id,
            'order_id' => $this->order_id,
            'user_id' => $this->user_id,
            'session_id' => $this->session_id,

            'order' => new OrderResource($this->whenLoaded('order')),
            'product' => new ProductResource($this->whenLoaded('product')),
            'wishlists' => WishlistResource::collection($this->whenLoaded('wishlists')),
        ];
    }
}
