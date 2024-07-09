<?php

namespace Modules\Front\Http\Requests;

use Modules\Core\Http\Requests\CoreRequest;

class ProductSearchRequest extends CoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string|array<string>>
     */
    public function rules(): array
    {
        return [
            'search' => 'required|string|max:255',
        ];
    }
}
