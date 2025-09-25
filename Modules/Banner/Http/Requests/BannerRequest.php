<?php

declare(strict_types=1);

namespace Modules\Banner\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;

class BannerRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return array_merge([
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'subtitle' => [
                'nullable',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'image' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:5120',
            ],
            'mobile_image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:5120',
            ],
            'link_url' => [
                'nullable',
                'url',
                'max:500',
            ],
            'link_text' => [
                'nullable',
                'string',
                'max:100',
            ],
            'position' => [
                'required',
                'string',
                'in:header,hero,sidebar,footer,popup',
            ],
            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
                'max:9999',
            ],
            'is_active' => [
                'boolean',
            ],
            'starts_at' => [
                'nullable',
                'date',
                'before_or_equal:expires_at',
            ],
            'expires_at' => [
                'nullable',
                'date',
                'after_or_equal:starts_at',
            ],
            'click_count' => [
                'nullable',
                'integer',
                'min:0',
                'max:999999',
            ],
            'view_count' => [
                'nullable',
                'integer',
                'min:0',
                'max:999999',
            ],
            'target_audience' => [
                'nullable',
                'array',
            ],
            'target_audience.*' => [
                'string',
                'max:100',
            ],
            'display_conditions' => [
                'nullable',
                'array',
            ],
            'display_conditions.*' => [
                'string',
                'max:255',
            ],
        ], $this->getCommonRules());
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'title.required' => 'Banner title is required.',
            'title.max' => 'Banner title must not exceed 255 characters.',
            'subtitle.max' => 'Banner subtitle must not exceed 255 characters.',
            'description.max' => 'Banner description must not exceed 1000 characters.',
            'image.required' => 'Banner image is required.',
            'image.max' => 'Banner image must not be larger than 5MB.',
            'mobile_image.max' => 'Mobile banner image must not be larger than 5MB.',
            'link_url.url' => 'Please enter a valid URL.',
            'link_url.max' => 'Link URL must not exceed 500 characters.',
            'link_text.max' => 'Link text must not exceed 100 characters.',
            'position.required' => 'Banner position is required.',
            'position.in' => 'Banner position must be header, hero, sidebar, footer, or popup.',
            'sort_order.min' => 'Sort order must be at least 0.',
            'sort_order.max' => 'Sort order cannot exceed 9999.',
            'starts_at.before_or_equal' => 'Start date must be before or equal to expiration date.',
            'expires_at.after_or_equal' => 'Expiration date must be after or equal to start date.',
            'click_count.min' => 'Click count must be at least 0.',
            'click_count.max' => 'Click count cannot exceed 999999.',
            'view_count.min' => 'View count must be at least 0.',
            'view_count.max' => 'View count cannot exceed 999999.',
            'target_audience.max' => 'Maximum 10 target audience segments are allowed.',
            'target_audience.*.max' => 'Each target audience segment must not exceed 100 characters.',
            'display_conditions.max' => 'Maximum 20 display conditions are allowed.',
            'display_conditions.*.max' => 'Each display condition must not exceed 255 characters.',
        ]);
    }

    /**
     * Additional validation rules.
     */
    protected function additionalValidation($validator): void
    {
        $validator->after(function ($validator): void {
            // Validate image dimensions based on position
            if ($this->hasFile('image')) {
                $image = $this->file('image');
                $imageInfo = getimagesize($image->getPathname());
                $position = $this->position;

                if ($imageInfo) {
                    $width = $imageInfo[0];
                    $height = $imageInfo[1];

                    switch ($position) {
                        case 'header':
                            if ($width < 1200 || $height < 100) {
                                $validator->errors()->add(
                                    'image',
                                    'Header banner must be at least 1200x100 pixels.'
                                );
                            }

                            break;
                        case 'hero':
                            if ($width < 1920 || $height < 600) {
                                $validator->errors()->add(
                                    'image',
                                    'Hero banner must be at least 1920x600 pixels.'
                                );
                            }

                            break;
                        case 'sidebar':
                            if ($width < 300 || $height < 250) {
                                $validator->errors()->add(
                                    'image',
                                    'Sidebar banner must be at least 300x250 pixels.'
                                );
                            }

                            break;
                        case 'footer':
                            if ($width < 1200 || $height < 200) {
                                $validator->errors()->add(
                                    'image',
                                    'Footer banner must be at least 1200x200 pixels.'
                                );
                            }

                            break;
                        case 'popup':
                            if ($width < 600 || $height < 400) {
                                $validator->errors()->add(
                                    'image',
                                    'Popup banner must be at least 600x400 pixels.'
                                );
                            }

                            break;
                    }

                    if ($width > 4000 || $height > 2000) {
                        $validator->errors()->add(
                            'image',
                            'Banner image must not exceed 4000x2000 pixels.'
                        );
                    }
                }
            }

            // Validate mobile image dimensions
            if ($this->hasFile('mobile_image')) {
                $mobileImage = $this->file('mobile_image');
                $imageInfo = getimagesize($mobileImage->getPathname());

                if ($imageInfo) {
                    $width = $imageInfo[0];
                    $height = $imageInfo[1];

                    if ($width < 320 || $height < 200) {
                        $validator->errors()->add(
                            'mobile_image',
                            'Mobile banner must be at least 320x200 pixels.'
                        );
                    }

                    if ($width > 1200 || $height > 800) {
                        $validator->errors()->add(
                            'mobile_image',
                            'Mobile banner must not exceed 1200x800 pixels.'
                        );
                    }
                }
            }

            // Validate link URL
            if ($this->filled('link_url')) {
                $linkUrl = $this->link_url;

                if (! filter_var($linkUrl, FILTER_VALIDATE_URL)) {
                    $validator->errors()->add(
                        'link_url',
                        'Please enter a valid URL.'
                    );
                }
            }

            // Validate link text
            if ($this->filled('link_text') && mb_strlen($this->link_text) > 100) {
                $validator->errors()->add(
                    'link_text',
                    'Link text must not exceed 100 characters.'
                );
            }

            // Validate sort order
            if ($this->filled('sort_order')) {
                $sortOrder = $this->sort_order;

                if ($sortOrder < 0 || $sortOrder > 9999) {
                    $validator->errors()->add(
                        'sort_order',
                        'Sort order must be between 0 and 9999.'
                    );
                }
            }

            // Validate date ranges
            if ($this->filled('starts_at') && $this->filled('expires_at')) {
                $startsAt = $this->starts_at;
                $expiresAt = $this->expires_at;

                if ($startsAt >= $expiresAt) {
                    $validator->errors()->add(
                        'expires_at',
                        'Expiration date must be after start date.'
                    );
                }
            }

            // Validate target audience
            if ($this->filled('target_audience')) {
                $targetAudience = $this->target_audience;

                if (count($targetAudience) > 10) {
                    $validator->errors()->add(
                        'target_audience',
                        'Maximum 10 target audience segments are allowed.'
                    );
                }

                foreach ($targetAudience as $index => $audience) {
                    if (mb_strlen($audience) < 2) {
                        $validator->errors()->add(
                            'target_audience.'.$index,
                            'Each target audience segment must be at least 2 characters long.'
                        );
                    }
                }
            }

            // Validate display conditions
            if ($this->filled('display_conditions')) {
                $displayConditions = $this->display_conditions;

                if (count($displayConditions) > 20) {
                    $validator->errors()->add(
                        'display_conditions',
                        'Maximum 20 display conditions are allowed.'
                    );
                }

                foreach ($displayConditions as $index => $condition) {
                    if (mb_strlen($condition) < 5) {
                        $validator->errors()->add(
                            'display_conditions.'.$index,
                            'Each display condition must be at least 5 characters long.'
                        );
                    }
                }
            }
        });
    }
}
