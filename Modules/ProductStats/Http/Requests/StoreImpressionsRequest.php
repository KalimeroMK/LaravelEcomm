<?php

namespace Modules\ProductStats\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreImpressionsRequest extends FormRequest
{
    public function rules()
    {
        return [
            'product_ids' => 'required|array',
            'product_ids.*' => 'integer',
        ];
    }
}
