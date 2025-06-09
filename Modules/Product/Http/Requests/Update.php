<?php

declare(strict_types=1);

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
            'stock' => 'required|numeric',
            'category' => 'required|array',
            'category.*' => 'required|exists:categories,id',
            'tag' => 'required|array',
            'tag.*' => 'required|exists:tags,id',
            'attributes' => 'sometimes|array',
            'attributes.*' => 'sometimes:', // or more specific rules
            'child_cat_id' => 'nullable|exists:categories,id',
            'is_featured' => 'sometimes|in:1',
            'brand_id' => 'nullable|exists:brands,id',
            'status' => 'required|in:active,inactive',
            'price' => 'required|numeric',
            'discount' => 'nullable|numeric',
        ];
    }
}
