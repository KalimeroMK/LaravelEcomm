<?php

namespace Modules\Bundle\Http\Requests\Api;

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
            'name' => ['required'],
            'description' => ['nullable'],
            'price' => ['required', 'numeric'],
            'product' => 'sometimes|array',
            'product.*' => 'required|exists:products,id',
            'images' => 'required|array',
            'images.*' => ['image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ];
    }
}
