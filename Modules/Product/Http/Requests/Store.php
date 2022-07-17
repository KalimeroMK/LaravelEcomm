<?php

namespace Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    /**
     * @var mixed
     */
    public mixed $category;
    
    public function rules(): array
    {
        return [
            'title'        => 'string|required',
            'summary'      => 'string|required',
            'description'  => 'string|nullable',
            'photo'        => 'required|image',
            'size'         => 'required',
            'color'        => 'required',
            'stock'        => "required|numeric",
            'category'     => 'required|exists:categories,id',
            'brand_id'     => 'nullable|exists:brands,id',
            'child_cat_id' => 'nullable|exists:categories,id',
            'is_featured'  => 'sometimes|in:1',
            'status'       => 'required|in:active,inactive',
            'condition'    => 'required|in:default,new,hot',
            'price'        => 'required|numeric',
            'discount'     => 'nullable|numeric',
        ];
    }
    
    public function authorize(): bool
    {
        return true;
    }
}
