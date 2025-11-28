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
        $banner = optional($this->route('banner'))->id;

        return [
            'title' => 'required|string|unique:banners,title,'.$banner.'|max:50',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'link' => 'nullable|url',
            'status' => 'nullable|in:active,inactive',
            'active_from' => 'nullable|date',
            'active_to' => 'nullable|date|after_or_equal:active_from',
            'max_clicks' => 'nullable|integer|min:0',
            'max_impressions' => 'nullable|integer|min:0',
            'categories' => 'array',
            'categories.*' => 'exists:categories,id',
        ];
    }
}
