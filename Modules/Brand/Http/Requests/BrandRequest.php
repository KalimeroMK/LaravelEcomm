<?php

declare(strict_types=1);

namespace Modules\Brand\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\BaseRequest;

class BrandRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $brandId = $this->route('brand') ?? $this->route('id');

        return array_merge([
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-_\.&]+$/',
            ],
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9\-]+$/',
                Rule::unique('brands', 'slug')->ignore($brandId),
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'website' => [
                'nullable',
                'url',
                'max:255',
            ],
            'logo' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp,svg',
                'max:2048',
            ],
            'banner' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:5120',
            ],
            'is_active' => [
                'boolean',
            ],
            'is_featured' => [
                'boolean',
            ],
            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
                'max:9999',
            ],
            'meta_title' => [
                'nullable',
                'string',
                'max:60',
            ],
            'meta_description' => [
                'nullable',
                'string',
                'max:160',
            ],
            'meta_keywords' => [
                'nullable',
                'string',
                'max:255',
            ],
            'social_links' => [
                'nullable',
                'array',
            ],
            'social_links.facebook' => [
                'nullable',
                'url',
                'max:255',
            ],
            'social_links.twitter' => [
                'nullable',
                'url',
                'max:255',
            ],
            'social_links.instagram' => [
                'nullable',
                'url',
                'max:255',
            ],
            'social_links.linkedin' => [
                'nullable',
                'url',
                'max:255',
            ],
            'social_links.youtube' => [
                'nullable',
                'url',
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
            'name.regex' => 'Brand name can only contain letters, numbers, spaces, hyphens, underscores, dots, and ampersands.',
            'slug.regex' => 'Slug can only contain lowercase letters, numbers, and hyphens.',
            'slug.unique' => 'This slug is already in use.',
            'website.url' => 'Please enter a valid website URL.',
            'logo.max' => 'Logo must not be larger than 2MB.',
            'banner.max' => 'Banner must not be larger than 5MB.',
            'sort_order.min' => 'Sort order must be at least 0.',
            'sort_order.max' => 'Sort order cannot exceed 9999.',
            'meta_title.max' => 'Meta title should not exceed 60 characters for SEO.',
            'meta_description.max' => 'Meta description should not exceed 160 characters for SEO.',
            'social_links.*.url' => 'Please enter a valid URL for social media links.',
        ]);
    }

    /**
     * Additional validation rules.
     */
    protected function additionalValidation($validator): void
    {
        $validator->after(function ($validator): void {
            // Validate website URL
            if ($this->filled('website')) {
                $website = $this->website;
                if (! filter_var($website, FILTER_VALIDATE_URL)) {
                    $validator->errors()->add(
                        'website',
                        'Please enter a valid website URL.'
                    );
                }
            }

            // Validate social links
            if ($this->filled('social_links')) {
                $socialLinks = $this->social_links;
                $validSocialPlatforms = ['facebook', 'twitter', 'instagram', 'linkedin', 'youtube'];

                foreach ($socialLinks as $platform => $url) {
                    if (! in_array($platform, $validSocialPlatforms)) {
                        $validator->errors()->add(
                            'social_links.'.$platform,
                            'Invalid social media platform.'
                        );
                    }

                    if (! empty($url) && ! filter_var($url, FILTER_VALIDATE_URL)) {
                        $validator->errors()->add(
                            'social_links.'.$platform,
                            'Please enter a valid URL for '.ucfirst($platform).'.'
                        );
                    }
                }
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

            // Validate meta title length for SEO
            if ($this->filled('meta_title') && mb_strlen($this->meta_title) > 60) {
                $validator->errors()->add(
                    'meta_title',
                    'Meta title should not exceed 60 characters for optimal SEO.'
                );
            }

            // Validate meta description length for SEO
            if ($this->filled('meta_description') && mb_strlen($this->meta_description) > 160) {
                $validator->errors()->add(
                    'meta_description',
                    'Meta description should not exceed 160 characters for optimal SEO.'
                );
            }

            // Validate logo dimensions
            if ($this->hasFile('logo')) {
                $logo = $this->file('logo');
                $imageInfo = getimagesize($logo->getPathname());

                if ($imageInfo) {
                    $width = $imageInfo[0];
                    $height = $imageInfo[1];

                    if ($width < 100 || $height < 100) {
                        $validator->errors()->add(
                            'logo',
                            'Logo must be at least 100x100 pixels.'
                        );
                    }

                    if ($width > 2000 || $height > 2000) {
                        $validator->errors()->add(
                            'logo',
                            'Logo must not exceed 2000x2000 pixels.'
                        );
                    }
                }
            }

            // Validate banner dimensions
            if ($this->hasFile('banner')) {
                $banner = $this->file('banner');
                $imageInfo = getimagesize($banner->getPathname());

                if ($imageInfo) {
                    $width = $imageInfo[0];
                    $height = $imageInfo[1];

                    if ($width < 800 || $height < 300) {
                        $validator->errors()->add(
                            'banner',
                            'Banner must be at least 800x300 pixels.'
                        );
                    }

                    if ($width > 4000 || $height > 2000) {
                        $validator->errors()->add(
                            'banner',
                            'Banner must not exceed 4000x2000 pixels.'
                        );
                    }
                }
            }
        });
    }
}
