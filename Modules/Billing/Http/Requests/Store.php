<?php

namespace Modules\Billing\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Modules\Billing\Rules\WishlistRule;
use Modules\Product\Models\Product;

class Store extends FormRequest
{
    public function rules(): array
    {
        return [
            'slug' => new WishlistRule(),
        ];
    }
    
    public function authorize(): bool
    {
        return true;
    }
    
    public function passedValidation()
    {
        $this->merge([
            'product_id' => Product::whereSlug(request()->slug)->first()->id,
            'quantity'   => 1,
            'user_id'    => Auth::id(),
            'discount'   => Product::whereSlug(request()->slug)->first()->discount,
            'price'      => Product::whereSlug(request()->slug)->first()->price,
        
        ]);
    }
}