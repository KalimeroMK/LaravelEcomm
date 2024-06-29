<?php

namespace Modules\Tenant\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'domain' => ['required'],
            'database' => ['required'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
