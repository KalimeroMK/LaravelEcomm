<?php

declare(strict_types=1);

namespace Modules\Product\Http\Requests\Api;

use Modules\Core\Http\Requests\Api\CoreRequest;

class SearchRequest extends CoreRequest
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
            'color' => 'nullable|string',
            'stock' => 'nullable|numeric',
            'brand_id' => 'nullable|numeric',
            'status' => 'nullable|in:active,inactive',
            'price' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'per_page' => 'nullable|int',
            'all_included' => 'nullable|boolean',
        ];
    }
}
