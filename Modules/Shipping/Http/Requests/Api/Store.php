<?php

declare(strict_types=1);

namespace Modules\Shipping\Http\Requests\Api;

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
            'type' => 'string|required|unique:shipping',
            'price' => 'nullable|numeric',
            'status' => 'required|in:active,inactive',
        ];
    }
}
