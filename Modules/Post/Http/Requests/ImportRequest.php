<?php

namespace Modules\Post\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'file' => 'required|mimes:xlsx, csv, xls',
        ];
    }
    
    public function authorize(): bool
    {
        return true;
    }
}