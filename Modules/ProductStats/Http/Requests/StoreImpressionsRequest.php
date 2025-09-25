<?php

declare(strict_types=1);

namespace Modules\ProductStats\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreImpressionsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'product_ids' => 'required|array',
            'product_ids.*' => 'integer',
        ];
    }
}
