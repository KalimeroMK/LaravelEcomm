<?php

declare(strict_types=1);

namespace Modules\Category\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\BaseRequest;

class CategoryRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $categoryId = $this->route('category') ?? $this->route('id');

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
                Rule::unique('categories', 'slug')->ignore($categoryId),
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'parent_id' => [
                'nullable',
                'integer',
                'exists:categories,id',
                'different:'.$categoryId,
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
            'is_featured' => [
                'boolean',
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
            'image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:2048',
            ],
            'icon' => [
                'nullable',
                'string',
                'max:100',
            ],
            'color' => [
                'nullable',
                'string',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],
        ], $this->getCommonRules());
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'name.regex' => 'Category name can only contain letters, numbers, spaces, hyphens, underscores, and dots.',
            'slug.regex' => 'Slug can only contain lowercase letters, numbers, and hyphens.',
            'slug.unique' => 'This slug is already in use.',
            'parent_id.exists' => 'Selected parent category does not exist.',
            'parent_id.different' => 'Category cannot be its own parent.',
            'sort_order.min' => 'Sort order must be at least 0.',
            'sort_order.max' => 'Sort order cannot exceed 9999.',
            'meta_title.max' => 'Meta title should not exceed 60 characters for SEO.',
            'meta_description.max' => 'Meta description should not exceed 160 characters for SEO.',
            'image.max' => 'Category image must not be larger than 2MB.',
            'color.regex' => 'Color must be a valid hex color code (e.g., #FF0000).',
        ]);
    }

    /**
     * Additional validation rules.
     */
    protected function additionalValidation($validator): void
    {
        $validator->after(function ($validator): void {
            // Validate parent category hierarchy
            if ($this->filled('parent_id')) {
                $parentId = $this->parent_id;
                $categoryId = $this->route('category') ?? $this->route('id');

                // Check if parent category exists and is active
                $parentCategory = \Modules\Category\Models\Category::find($parentId);
                if (! $parentCategory) {
                    $validator->errors()->add(
                        'parent_id',
                        'Parent category does not exist.'
                    );
                } elseif (! $parentCategory->is_active) {
                    $validator->errors()->add(
                        'parent_id',
                        'Parent category must be active.'
                    );
                }

                // Check for circular reference
                if ($categoryId && $this->isCircularReference($categoryId, $parentId)) {
                    $validator->errors()->add(
                        'parent_id',
                        'Cannot set parent category as it would create a circular reference.'
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
        });
    }

    /**
     * Check for circular reference in category hierarchy.
     */
    private function isCircularReference(int $categoryId, int $parentId): bool
    {
        $currentParent = $parentId;

        while ($currentParent) {
            if ($currentParent === $categoryId) {
                return true;
            }

            $parent = \Modules\Category\Models\Category::find($currentParent);
            if (! $parent || ! $parent->parent_id) {
                break;
            }

            $currentParent = $parent->parent_id;
        }

        return false;
    }
}
