<?php

declare(strict_types=1);

namespace Modules\Order\Http\Requests\Api;

use Illuminate\Validation\Rule;
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
            'status' => ['required', Rule::in(['new', 'process', 'delivered', 'cancel'])],
        ];
    }
}
