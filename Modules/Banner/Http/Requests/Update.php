<?php

declare(strict_types=1);

namespace Modules\Banner\Http\Requests;

use Modules\Core\Http\Requests\CoreRequest;

class Update extends CoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string> Array of field rules.
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => ['required', 'in:active,inactive'],
            'active_from' => ['nullable', 'date'],
            'active_to' => ['nullable', 'date', 'after_or_equal:active_from'],
            'max_clicks' => ['nullable', 'integer', 'min:0'],
            'max_impressions' => ['nullable', 'integer', 'min:0'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['integer', 'exists:categories,id'],
        ];
    }
}
