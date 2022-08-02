<?php

namespace Modules\Newsletter\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:newsletters',
        ];
    }
    
    public function authorize(): bool
    {
        return true;
    }
}