<?php

namespace Modules\Billing\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Modules\Billing\Rules\WishlistRule;
use Modules\Core\Http\Requests\CoreRequest;
use Modules\Product\Models\Product;

class Store extends CoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed> // 'mixed' to indicate various possible types including custom rule objects
     */
    public function rules(): array
    {
        return [
            'slug' => new WishlistRule(),  // Custom rule object clearly indicated
        ];
    }

    /**
     * Handle a passed validation attempt.
     * Merge additional fields into the request data based on the 'slug' provided.
     */
    public function passedValidation(): void
    {
        $product = Product::whereSlug($this->slug)->firstOrFail();

        $this->merge([
            'product_id' => $product->id,
            'quantity' => 1,
            'user_id' => Auth::id(),
            'discount' => $product->discount,
            'price' => $product->price,
        ]);
    }
}
