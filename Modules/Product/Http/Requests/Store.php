<?php

namespace Modules\Product\Http\Requests;

use Modules\Core\Http\Requests\CoreRequest;

class Store extends CoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string|array<string>>
     */
    public function rules(): array
    {
        return [
            'title' => 'string|required',
            'summary' => 'string|required',
            'description' => 'string|nullable',
            'sku' => 'string|nullable|unique:products,sku',
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'size' => 'required',
            'color' => 'required',
            'category' => 'sometimes|array',
            'category.*' => 'required|exists:categories,id',
            'condition_id' => 'required|exists:conditions,id',
            'stock' => 'required|numeric',
            'brand_id' => 'nullable|exists:brands,id',
            'child_cat_id' => 'nullable|exists:categories,id',
            'is_featured' => 'sometimes|in:1',
            'status' => 'required|in:active,inactive',
            'price' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'special_price_start' => 'nullable|date',
            'special_price_end' => 'nullable|date|after:special_price_start',
            'special_price' => 'nullable',
        ];
    }

}
