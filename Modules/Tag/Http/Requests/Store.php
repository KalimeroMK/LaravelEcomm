<?php

namespace Modules\Tag\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    public function rules(): array
    {
        return [
            'title'  => 'string|required',
            'status' => 'required|in:active,inactive',
        ];
    }
    
    public function authorize(): bool
    {
        return true;
    }
}
