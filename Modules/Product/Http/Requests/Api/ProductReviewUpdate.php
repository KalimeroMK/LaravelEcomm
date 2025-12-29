<?php

declare(strict_types=1);

namespace Modules\Product\Http\Requests\Api;

use Modules\Core\Http\Requests\Api\CoreRequest;

class ProductReviewUpdate extends CoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string|array<string>>
     */
    public function rules(): array
    {
        return [
            'review' => 'sometimes|required|string',
            'rate' => 'sometimes|required|numeric|min:1|max:5',
            'status' => 'sometimes|in:active,inactive',
        ];
    }
}
