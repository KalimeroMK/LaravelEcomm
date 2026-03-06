<?php

declare(strict_types=1);

namespace Modules\Language\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LanguageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $languageId = $this->route('language')?->id;

        return [
            'code' => [
                'required',
                'string',
                'size:2',
                Rule::unique('languages', 'code')->ignore($languageId),
            ],
            'name' => 'required|string|max:255',
            'native_name' => 'required|string|max:255',
            'flag' => 'nullable|string|max:10',
            'direction' => 'required|in:ltr,rtl',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Language code is required.',
            'code.size' => 'Language code must be exactly 2 characters.',
            'code.unique' => 'This language code already exists.',
            'name.required' => 'Language name is required.',
            'native_name.required' => 'Native name is required.',
            'direction.required' => 'Text direction is required.',
            'direction.in' => 'Direction must be LTR or RTL.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'is_default' => $this->boolean('is_default'),
            'sort_order' => $this->input('sort_order', 0),
        ]);
    }
}
