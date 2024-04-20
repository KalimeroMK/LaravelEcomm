<?php

namespace Modules\Banner\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string> Array of field rules.
     */
    public function rules(): array
    {
        return [
            'title' => 'string|required|max:50|unique:banners',
            'description' => 'string|nullable',
            'photo' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'status' => 'required|in:active,inactive',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
