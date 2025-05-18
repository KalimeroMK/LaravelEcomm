<?php

declare(strict_types=1);

namespace Modules\Product\Http\Requests\Api;

use Modules\Core\Http\Requests\Api\CoreRequest;

class Update extends CoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string|array<string>>
     */
    public function rules(): array
    {
        return [
            'title' => 'string|nullable',
            'summary' => 'string|nullable',
            'description' => 'string|nullable',
            'sku' => 'nullable|string|unique:products,sku',
            'category' => 'sometimes|array',
            'category.*' => 'nullable|exists:categories,id',
            'tag' => 'sometimes|array',
            'tag.*' => 'nullable|exists:tags,id',
            'stock' => 'nullable|numeric',
            'brand_id' => 'nullable|exists:brands,id',
            'is_featured' => 'sometimes|boolean',
            'status' => 'nullable|in:active,inactive',
            'price' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
        ];
    }
}
