<?php

declare(strict_types=1);

namespace Modules\Post\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\BaseRequest;

class PostRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $postId = $this->route('post') ?? $this->route('id');

        return array_merge([
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9\-]+$/',
                Rule::unique('posts', 'slug')->ignore($postId),
            ],
            'content' => [
                'required',
                'string',
                'max:50000',
            ],
            'excerpt' => [
                'nullable',
                'string',
                'max:500',
            ],
            'status' => [
                'required',
                'string',
                'in:draft,published,archived',
            ],
            'featured_image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:5120',
            ],
            'author_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],
            'category_id' => [
                'nullable',
                'integer',
                'exists:categories,id',
            ],
            'tags' => [
                'nullable',
                'array',
                'max:20',
            ],
            'tags.*' => [
                'string',
                'max:50',
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
            'is_featured' => [
                'boolean',
            ],
            'allow_comments' => [
                'boolean',
            ],
            'published_at' => [
                'nullable',
                'date',
                'before_or_equal:now',
            ],
            'reading_time' => [
                'nullable',
                'integer',
                'min:1',
                'max:999',
            ],
        ], $this->getCommonRules());
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'title.required' => 'Post title is required.',
            'title.max' => 'Post title must not exceed 255 characters.',
            'slug.required' => 'Post slug is required.',
            'slug.regex' => 'Slug can only contain lowercase letters, numbers, and hyphens.',
            'slug.unique' => 'This slug is already in use.',
            'content.required' => 'Post content is required.',
            'content.min' => 'Post content must be at least 100 characters long.',
            'content.max' => 'Post content must not exceed 50000 characters.',
            'excerpt.max' => 'Post excerpt must not exceed 500 characters.',
            'status.required' => 'Post status is required.',
            'status.in' => 'Post status must be draft, published, or archived.',
            'featured_image.max' => 'Featured image must not be larger than 5MB.',
            'author_id.required' => 'Post author is required.',
            'author_id.exists' => 'Selected author does not exist.',
            'category_id.exists' => 'Selected category does not exist.',
            'tags.max' => 'Maximum 20 tags are allowed.',
            'tags.*.max' => 'Each tag must not exceed 50 characters.',
            'meta_title.max' => 'Meta title should not exceed 60 characters for SEO.',
            'meta_description.max' => 'Meta description should not exceed 160 characters for SEO.',
            'reading_time.min' => 'Reading time must be at least 1 minute.',
            'reading_time.max' => 'Reading time cannot exceed 999 minutes.',
        ]);
    }

    /**
     * Additional validation rules.
     */
    protected function additionalValidation($validator): void
    {
        $validator->after(function ($validator): void {
            // Validate slug format
            if ($this->filled('slug')) {
                $slug = $this->slug;

                if (mb_strlen($slug) < 3) {
                    $validator->errors()->add(
                        'slug',
                        'Slug must be at least 3 characters long.'
                    );
                }

                if (mb_strlen($slug) > 100) {
                    $validator->errors()->add(
                        'slug',
                        'Slug must not exceed 100 characters.'
                    );
                }
            }

            // Validate content length
            if ($this->filled('content')) {
                $content = $this->content;

                if (mb_strlen($content) < 100) {
                    $validator->errors()->add(
                        'content',
                        'Content must be at least 100 characters long.'
                    );
                }

                if (mb_strlen($content) > 50000) {
                    $validator->errors()->add(
                        'content',
                        'Content must not exceed 50000 characters.'
                    );
                }
            }

            // Validate excerpt length
            if ($this->filled('excerpt') && mb_strlen($this->excerpt) > 500) {
                $validator->errors()->add(
                    'excerpt',
                    'Excerpt must not exceed 500 characters.'
                );
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

            // Validate tags
            if ($this->filled('tags')) {
                $tags = $this->tags;

                if (count($tags) > 20) {
                    $validator->errors()->add(
                        'tags',
                        'Maximum 20 tags are allowed.'
                    );
                }

                foreach ($tags as $index => $tag) {
                    if (mb_strlen($tag) < 2) {
                        $validator->errors()->add(
                            'tags.'.$index,
                            'Each tag must be at least 2 characters long.'
                        );
                    }

                    if (mb_strlen($tag) > 50) {
                        $validator->errors()->add(
                            'tags.'.$index,
                            'Each tag must not exceed 50 characters.'
                        );
                    }
                }
            }

            // Validate reading time
            if ($this->filled('reading_time')) {
                $readingTime = $this->reading_time;

                if ($readingTime < 1) {
                    $validator->errors()->add(
                        'reading_time',
                        'Reading time must be at least 1 minute.'
                    );
                }

                if ($readingTime > 999) {
                    $validator->errors()->add(
                        'reading_time',
                        'Reading time cannot exceed 999 minutes.'
                    );
                }
            }

            // Validate featured image dimensions
            if ($this->hasFile('featured_image')) {
                $image = $this->file('featured_image');
                $imageInfo = getimagesize($image->getPathname());

                if ($imageInfo) {
                    $width = $imageInfo[0];
                    $height = $imageInfo[1];

                    if ($width < 800 || $height < 400) {
                        $validator->errors()->add(
                            'featured_image',
                            'Featured image must be at least 800x400 pixels.'
                        );
                    }

                    if ($width > 4000 || $height > 2000) {
                        $validator->errors()->add(
                            'featured_image',
                            'Featured image must not exceed 4000x2000 pixels.'
                        );
                    }
                }
            }

            // Validate published date
            if ($this->filled('published_at')) {
                $publishedAt = $this->published_at;

                if ($publishedAt > now()) {
                    $validator->errors()->add(
                        'published_at',
                        'Published date cannot be in the future.'
                    );
                }
            }
        });
    }
}
