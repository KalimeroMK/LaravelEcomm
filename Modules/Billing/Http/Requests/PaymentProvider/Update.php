<?php

declare(strict_types=1);

namespace Modules\Billing\Http\Requests\PaymentProvider;

use Modules\Core\Http\Requests\CoreRequest;

class Update extends CoreRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'public_key' => ['nullable'],
            'secret_key' => ['nullable'],
            'status' => ['boolean'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
