<?php

namespace Modules\Product\Http\Requests;

use Modules\Core\Http\Requests\CoreRequest;

class Update extends CoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string|string[]>
     */
    public function rules(): array
    {
        return [
            'title' => 'string|required',
            'summary' => 'string|required',
            'sku' => 'string|nullable|unique:products,sku',
            'description' => 'string|nullable',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'size' => 'sometimes|array',
            'size.*' => 'required|exists:sizes,id',
            'color' => 'nullable',
            'stock' => 'required|numeric',
            'category' => 'sometimes|array',
            'category.*' => 'required|exists:categories,id',
            'child_cat_id' => 'nullable|exists:categories,id',
            'is_featured' => 'sometimes|in:1',
            'brand_id' => 'nullable|exists:brands,id',
            'status' => 'required|in:active,inactive',
            'condition_id' => 'required|exists:conditions,id',
            'price' => 'required|numeric',
            'discount' => 'nullable|numeric',
        ];
    }

}
