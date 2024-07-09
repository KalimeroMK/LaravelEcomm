<?php

namespace Modules\Product\Http\Requests;

use Modules\Core\Http\Requests\CoreRequest;

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
            'rate' => 'required|numeric|min:1',
        ];
    }

}
