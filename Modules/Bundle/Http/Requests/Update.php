<?php

namespace Modules\Bundle\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'description' => ['nullable'],
            'price' => ['required', 'numeric'],
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
