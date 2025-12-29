<?php

declare(strict_types=1);

namespace Modules\Coupon\Http\Requests;

use Modules\Core\Http\Requests\CoreRequest;

class Update extends CoreRequest
{
    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'code' => 'sometimes|string|max:50',
            'type' => 'sometimes|in:fixed,percent|string',
            'value' => 'sometimes|numeric',
            'status' => 'sometimes|in:active,inactive|string',
            'expires_at' => 'sometimes|date|after:now',
        ];
    }
}
