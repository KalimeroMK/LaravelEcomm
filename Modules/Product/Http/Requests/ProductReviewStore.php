<?php

namespace Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductReviewStore extends FormRequest
{
    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'rate' => 'required|numeric|min:1',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
