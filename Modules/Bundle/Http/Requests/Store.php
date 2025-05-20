<?php

declare(strict_types=1);

namespace Modules\Bundle\Http\Requests;

use Modules\Core\Http\Requests\CoreRequest;

class Store extends CoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string|array<string>> Array of validation rules where values can be strings or arrays of strings.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'description' => 'nullable',
            'price' => 'required|numeric',
            'products' => 'sometimes|array',
            'products.*' => 'required|exists:products,id',
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }
}
