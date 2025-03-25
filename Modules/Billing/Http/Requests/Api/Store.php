<?php

declare(strict_types=1);

namespace Modules\Billing\Http\Requests\Api;

use Illuminate\Support\Facades\Auth;
use Modules\Billing\Rules\WishlistRule;
use Modules\Core\Http\Requests\Api\CoreRequest;
use Modules\Product\Models\Product;

class Store extends CoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'slug' => ['required', new WishlistRule],
        ];
    }

    public function passedValidation(): void
    {
        $this->merge([
            'product_id' => Product::whereSlug($this->get('slug'))->first()->id,
            'quantity' => 1,
            'user_id' => Auth::id(),
            'discount' => Product::whereSlug($this->get('slug'))->first()->discount,
            'price' => Product::whereSlug($this->get('slug'))->first()->price,

        ]);
    }
}
