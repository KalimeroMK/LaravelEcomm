<?php

declare(strict_types=1);

namespace Modules\Product\Http\Requests\Api;

use Modules\Core\Http\Requests\Api\CoreRequest;

class ProductReviewStore extends CoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string|array<string>>
     */
    public function rules(): array
    {
        return [
            'review' => 'required|string',
            'product_id' => 'required|exists:products,id',
            'rate' => 'required|numeric|min:1|max:5',
        ];
    }
}
