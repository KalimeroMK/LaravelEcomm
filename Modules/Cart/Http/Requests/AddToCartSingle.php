<?php

namespace Modules\Cart\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Cart\Rules\ProductStockRule;

class AddToCartSingle extends FormRequest
{
    
    /**
     * @var mixed
     */
    public mixed $slug;
    /**
     * @var mixed
     */
    public mixed $quantity;
    
    public function rules(): array
    {
        return [
            'slug'     => 'required|exists:products',
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
