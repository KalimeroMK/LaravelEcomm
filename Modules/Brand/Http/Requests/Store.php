<?php

namespace Modules\Brand\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'string|required|unique:brands',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
