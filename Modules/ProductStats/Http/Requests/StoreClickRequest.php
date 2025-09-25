<?php

declare(strict_types=1);

namespace Modules\ProductStats\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClickRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'product_id' => 'required|integer',
        ];
    }
}
