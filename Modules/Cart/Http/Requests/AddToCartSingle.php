<?php

namespace Modules\Cart\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Cart\Rules\ProductStockRule;

class AddToCartSingle extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string> Array of field rules.
     */
    public function rules(): array
    {
        return [
            'slug' => 'required|exists:products',
            'quantity' => [
                'required',
                new ProductStockRule(),
            ],

        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
