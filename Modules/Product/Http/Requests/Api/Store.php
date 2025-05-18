<?php

declare(strict_types=1);

namespace Modules\Product\Http\Requests\Api;

use Modules\Core\Http\Requests\Api\CoreRequest;

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
            'sku' => 'nullable|string|unique:products,sku',
            'category' => 'sometimes|array',
            'category.*' => 'required|exists:categories,id',
            'tag' => 'sometimes|array',
            'tag.*' => 'required|exists:tags,id',
            'stock' => 'required|numeric',
            'brand_id' => 'nullable|exists:brands,id',
            'is_featured' => 'sometimes|boolean',
            'status' => 'required|in:active,inactive',
            'price' => 'required|numeric',
            'discount' => 'nullable|numeric',
        ];
    }
}
