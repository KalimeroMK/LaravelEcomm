<?php

namespace Modules\Billing\Http\Requests;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Modules\Billing\Rules\WishlistRule;
use Modules\Product\Models\Product;

class Store extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, Rule|string>
     */
    public function rules(): array
    {
        return [
            'slug' => new WishlistRule(),  // Custom rule object
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Handle a passed validation attempt.
     * Merge additional fields into the request data.
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
