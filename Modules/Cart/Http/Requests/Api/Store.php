<?php

namespace Modules\Cart\Http\Requests\Api;

use Modules\Cart\Rules\ProductStockRule;
use Modules\Core\Http\Requests\Api\CoreRequest;

class Store extends CoreRequest
{
    public function rules(): array
    {
        return [
            'slug'  => 'string|required|exists:products,slug',
            'quant' => [
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