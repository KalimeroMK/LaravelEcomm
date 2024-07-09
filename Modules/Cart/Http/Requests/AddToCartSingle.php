<?php

namespace Modules\Cart\Http\Requests;

use Modules\Cart\Rules\ProductStockRule;
use Modules\Core\Http\Requests\CoreRequest;

class AddToCartSingle extends CoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string|array<int, string|ProductStockRule>>
     * The returned array keys are field names, and values are either validation rule strings or arrays of mixed validation rules.
     */
    public function rules(): array
    {
        return [
            'slug' => 'required|exists:products',  // Single rule string
            'quantity' => [
                'required',              // Single rule string within an array
                new ProductStockRule(),  // Custom validation rule object
            ],
        ];
    }
}
