<?php

namespace Modules\Page\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required'],
            'slug' => ['required'],
            'content' => ['required'],
            'is_active' => ['boolean'],
            'user_id' => ['required', 'exists:users'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
