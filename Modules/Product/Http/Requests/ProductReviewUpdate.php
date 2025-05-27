<?php

declare(strict_types=1);

namespace Modules\Product\Http\Requests;

use Modules\Core\Http\Requests\CoreRequest;

class ProductReviewUpdate extends CoreRequest
{
    public function rules(): array
    {
        return [
            'rating' => 'required|numeric|min:1',
            'review' => 'required|string',
        ];
    }
}
