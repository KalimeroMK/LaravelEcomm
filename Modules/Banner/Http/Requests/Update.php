<?php

namespace Modules\Banner\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    public function rules(): array
    {
        return [
            'title'       => 'string|required|max:50',
            'description' => 'string|nullable',
            'photo'       => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'status'      => 'required|in:active,inactive',
        ];
    }
    
    public function authorize(): bool
    {
        return true;
    }
}
