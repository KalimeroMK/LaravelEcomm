<?php

declare(strict_types=1);

namespace Modules\Bundle\Http\Requests\Api;

use Modules\Core\Http\Requests\CoreRequest;

class Update extends CoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string|array<string>> Array of validation rules where values can be strings or arrays of strings.
     */
    public function rules(): array
    {
        return [
            'name' => ['nullable|string|max:50'],
            'description' => ['nullable'],
            'products' => 'nullable|array',
            'products.*' => 'required|exists:products,id',
            'price' => ['nullable', 'numeric'],
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }
}
