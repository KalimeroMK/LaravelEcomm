<?php

namespace Modules\Order\Http\Requests\Api;

use Modules\Core\Http\Requests\Api\CoreRequest;

class Update extends CoreRequest
{
    public mixed $shipping;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string|array<string>>
     */
    public function rules(): array
    {
        return [
            'status' => 'required|in:new,process,delivered,cancel',
        ];
    }
}
