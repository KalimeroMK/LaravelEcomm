<?php

namespace Modules\Page\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string'],
            'slug' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'user_id' => ['nullable', 'exists:users'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
