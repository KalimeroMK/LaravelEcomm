<?php

namespace Modules\Order\Http\Requests\Api;

use Modules\Core\Http\Requests\Api\CoreRequest;

class Update extends CoreRequest
{
    /**
     * @var mixed
     */
    public mixed $shipping;
    
    public function rules(): array
    {
        return [
            'status' => 'required|in:new,process,delivered,cancel',
        ];
    }
    
    public function authorize(): bool
    {
        return true;
    }
}
