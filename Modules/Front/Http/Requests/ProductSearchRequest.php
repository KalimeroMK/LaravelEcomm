<?php

namespace Modules\Front\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductSearchRequest extends FormRequest
{
    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'search' => 'required|string|max:255',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
