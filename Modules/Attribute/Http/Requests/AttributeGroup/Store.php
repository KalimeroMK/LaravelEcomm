<?php

declare(strict_types=1);

namespace Modules\Attribute\Http\Requests\AttributeGroup;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'attributes' => 'nullable|array',
            'attributes.*' => 'integer|exists:attributes,id',
        ];
    }
}
