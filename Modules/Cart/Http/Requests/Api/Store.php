<?php

namespace Modules\Cart\Http\Requests\Api;

use Modules\Cart\Rules\ProductStockRule;
use Modules\Core\Http\Requests\Api\CoreRequest;

class Store extends CoreRequest
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
            'slug' => 'string|required|exists:products,slug',
            'quantity' => [
                'required',
                new ProductStockRule(),
            ],

        ];
    }
}
