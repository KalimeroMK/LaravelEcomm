<?php

namespace Modules\Bundle\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'description' => ['nullable'],
            'price' => ['required', 'numeric'],
            'product' => 'sometimes|array',
            'product.*' => 'required|exists:products,id',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
