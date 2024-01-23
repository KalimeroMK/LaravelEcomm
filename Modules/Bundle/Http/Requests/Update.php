<?php

namespace Modules\Bundle\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'description' => ['nullable'],
            'price' => ['required', 'numeric'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
