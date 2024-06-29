<?php

namespace Modules\Tenant\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['nullable'],
            'domain' => ['nullable'],
            'database' => ['nullable'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
