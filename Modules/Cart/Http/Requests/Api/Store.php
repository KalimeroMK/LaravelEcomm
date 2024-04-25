<?php

namespace Modules\Cart\Http\Requests\Api;

use Modules\Cart\Rules\ProductStockRule;
use Modules\Core\Http\Requests\Api\CoreRequest;

class Store extends CoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string> Array of field rules.
     */

    public function rules(): array
    {
        return [
            'slug' => 'string|required|exists:products,slug',
            'quantity' => [
                'required',
                new ProductStockRule(),
            ],

        ];
    }
}