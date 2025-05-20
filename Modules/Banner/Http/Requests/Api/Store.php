<?php

declare(strict_types=1);

namespace Modules\Banner\Http\Requests\Api;

use Modules\Core\Http\Requests\Api\CoreRequest;

class Store extends CoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string> Array of field rules.
     */
    public function rules(): array
    {
        return [
            'title' => 'string|required|max:50|unique:banners',
            'description' => 'string|nullable',
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required|in:active,inactive',
            'slug' => 'required|unique:banners,slug',
        ];
    }
}
