<?php

namespace Modules\Order\Http\Requests;

use Modules\Core\Http\Requests\CoreRequest;

class Update extends CoreRequest
{
    /**
     * Shipping information.
     *
     * @var string[]
     */
    public mixed $shipping;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'status' => 'required|in:new,process,delivered,cancel',
        ];
    }
}
