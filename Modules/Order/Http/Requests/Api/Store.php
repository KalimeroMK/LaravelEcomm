<?php

declare(strict_types=1);

namespace Modules\Order\Http\Requests\Api;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\Api\CoreRequest;

class Store extends CoreRequest
{
    public function rules(): array
    {
        return [
            'order_number' => ['nullable'],
            'user_id' => ['nullable', 'exists:users,id'],
            'sub_total' => ['required', 'numeric'],
            'shipping_id' => ['nullable', 'exists:shipping,id'],
            'total_amount' => ['required', 'numeric'],
            'quantity' => ['required', 'integer'],
            'payment_method' => ['required'],
            'payment_status' => ['required'],
            'status' => ['required', Rule::in(['pending', 'processing', 'shipped', 'delivered', 'cancelled'])],
            'payer_id' => ['nullable'],
            'transaction_reference' => ['nullable'],

        ];
    }
}
