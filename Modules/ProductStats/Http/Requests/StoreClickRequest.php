<?php

namespace Modules\ProductStats\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClickRequest extends FormRequest
{
    public function rules()
    {
        return [
            'product_id' => 'required|integer',
        ];
    }
}
