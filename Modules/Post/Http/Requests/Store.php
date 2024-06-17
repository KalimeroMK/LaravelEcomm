<?php

namespace Modules\Post\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'title' => 'string|required',
            'quote' => 'string|nullable',
            'summary' => 'string|required',
            'description' => 'string|nullable',
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'category' => 'sometimes|array',
            'category.*' => 'required|exists:categories,id',
            'added_by' => 'nullable',
            'status' => 'required|in:active,inactive',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
