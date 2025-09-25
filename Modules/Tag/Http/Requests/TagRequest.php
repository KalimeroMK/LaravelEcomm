<?php

declare(strict_types=1);

namespace Modules\Tag\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\BaseRequest;

class TagRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $tagId = $this->route('tag') ?? $this->route('id');

        return array_merge([
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-_\.]+$/',
            ],
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9\-]+$/',
                Rule::unique('tags', 'slug')->ignore($tagId),
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'color' => [
                'nullable',
                'string',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],
            'is_active' => [
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
            'icon' => [
                'nullable',
                'string',
                'max:100',
            ],
            'image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp,svg',
                'max:2048',
            ],
        ], $this->getCommonRules());
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'name.required' => 'Tag name is required.',
            'name.regex' => 'Tag name can only contain letters, numbers, spaces, hyphens, underscores, and dots.',
            'name.min' => 'Tag name must be at least 2 characters long.',
            'name.max' => 'Tag name must not exceed 255 characters.',
            'slug.required' => 'Tag slug is required.',
            'slug.regex' => 'Tag slug can only contain lowercase letters, numbers, and hyphens.',
            'slug.unique' => 'This slug is already in use.',
            'slug.min' => 'Tag slug must be at least 2 characters long.',
            'slug.max' => 'Tag slug must not exceed 100 characters.',
            'description.max' => 'Tag description must not exceed 1000 characters.',
            'color.regex' => 'Color must be a valid hex color code (e.g., #FF0000).',
            'sort_order.min' => 'Sort order must be at least 0.',
            'sort_order.max' => 'Sort order cannot exceed 9999.',
            'meta_title.max' => 'Meta title should not exceed 60 characters for SEO.',
            'meta_description.max' => 'Meta description should not exceed 160 characters for SEO.',
            'icon.max' => 'Icon must not exceed 100 characters.',
            'image.max' => 'Tag image must not be larger than 2MB.',
        ]);
    }

    /**
     * Additional validation rules.
     */
    protected function additionalValidation($validator): void
    {
        $validator->after(function ($validator): void {
            // Validate name length
            if ($this->filled('name')) {
                $name = $this->name;

                if (mb_strlen($name) < 2) {
                    $validator->errors()->add(
                        'name',
                        'Tag name must be at least 2 characters long.'
                    );
                }

                if (mb_strlen($name) > 255) {
                    $validator->errors()->add(
                        'name',
                        'Tag name must not exceed 255 characters.'
                    );
                }
            }

            // Validate slug length
            if ($this->filled('slug')) {
                $slug = $this->slug;

                if (mb_strlen($slug) < 2) {
                    $validator->errors()->add(
                        'slug',
                        'Tag slug must be at least 2 characters long.'
                    );
                }

                if (mb_strlen($slug) > 100) {
                    $validator->errors()->add(
                        'slug',
                        'Tag slug must not exceed 100 characters.'
                    );
                }
            }

            // Validate description length
            if ($this->filled('description') && mb_strlen($this->description) > 1000) {
                $validator->errors()->add(
                    'description',
                    'Tag description must not exceed 1000 characters.'
                );
            }

            // Validate color format
            if ($this->filled('color')) {
                $color = $this->color;

                if (! preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
                    $validator->errors()->add(
                        'color',
                        'Color must be a valid hex color code (e.g., #FF0000).'
                    );
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

            // Validate icon length
            if ($this->filled('icon') && mb_strlen($this->icon) > 100) {
                $validator->errors()->add(
                    'icon',
                    'Icon must not exceed 100 characters.'
                );
            }

            // Validate image dimensions
            if ($this->hasFile('image')) {
                $image = $this->file('image');
                $imageInfo = getimagesize($image->getPathname());

                if ($imageInfo) {
                    $width = $imageInfo[0];
                    $height = $imageInfo[1];

                    if ($width < 100 || $height < 100) {
                        $validator->errors()->add(
                            'image',
                            'Tag image must be at least 100x100 pixels.'
                        );
                    }

                    if ($width > 2000 || $height > 2000) {
                        $validator->errors()->add(
                            'image',
                            'Tag image must not exceed 2000x2000 pixels.'
                        );
                    }
                }
            }

            // Validate tag name uniqueness (case insensitive)
            if ($this->filled('name')) {
                $name = $this->name;
                $tagId = $this->route('tag') ?? $this->route('id');

                $existingTag = \Modules\Tag\Models\Tag::where('name', 'LIKE', $name)
                    ->where('id', '!=', $tagId)
                    ->first();

                if ($existingTag) {
                    $validator->errors()->add(
                        'name',
                        'A tag with this name already exists.'
                    );
                }
            }

            // Validate tag slug uniqueness (case insensitive)
            if ($this->filled('slug')) {
                $slug = $this->slug;
                $tagId = $this->route('tag') ?? $this->route('id');

                $existingTag = \Modules\Tag\Models\Tag::where('slug', 'LIKE', $slug)
                    ->where('id', '!=', $tagId)
                    ->first();

                if ($existingTag) {
                    $validator->errors()->add(
                        'slug',
                        'A tag with this slug already exists.'
                    );
                }
            }
        });
    }
}
