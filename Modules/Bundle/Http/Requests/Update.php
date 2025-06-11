<?php

declare(strict_types=1);

namespace Modules\Bundle\Http\Requests;

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
            'title' => 'required|string|unique:bundles,title|max:50',
            'name' => 'nullable|string',
            'description' => 'nullable',
            'price' => 'nullable|numeric',
            'products' => 'nullable|array',
            'products.*' => 'required|exists:products,id',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }
}
