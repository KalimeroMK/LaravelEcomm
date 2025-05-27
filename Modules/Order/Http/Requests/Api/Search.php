<?php

declare(strict_types=1);

namespace Modules\Order\Http\Requests\Api;

use Modules\Core\Http\Requests\Api\CoreRequest;

class Search extends CoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string|array<string>>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'string|nullable',
            'last_name' => 'string|nullable',
            'address1' => 'string|nullable',
            'address2' => 'string|nullable',
            'coupon' => 'nullable|numeric',
            'phone' => 'numeric|nullable',
            'post_code' => 'string|nullable',
            'email' => 'string|nullable',
            'per_page' => 'nullable|int',
            'all_included' => 'nullable|boolean',
            'user_id' => 'nullable|exists:users',
        ];
    }
}
