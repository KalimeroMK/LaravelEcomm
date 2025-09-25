<?php

declare(strict_types=1);

namespace Modules\Page\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\BaseRequest;

class PageRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $pageId = $this->route('page') ?? $this->route('id');

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
                Rule::unique('pages', 'slug')->ignore($pageId),
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
            'parent_id' => [
                'nullable',
                'integer',
                'exists:pages,id',
                'different:'.$pageId,
            ],
            'template' => [
                'nullable',
                'string',
                'max:100',
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
            'is_homepage' => [
                'boolean',
            ],
            'is_blog' => [
                'boolean',
            ],
            'is_contact' => [
                'boolean',
            ],
            'is_about' => [
                'boolean',
            ],
            'is_privacy' => [
                'boolean',
            ],
            'is_terms' => [
                'boolean',
            ],
            'is_faq' => [
                'boolean',
            ],
            'is_help' => [
                'boolean',
            ],
            'is_support' => [
                'boolean',
            ],
            'is_documentation' => [
                'boolean',
            ],
            'is_tutorial' => [
                'boolean',
            ],
            'is_guide' => [
                'boolean',
            ],
            'is_manual' => [
                'boolean',
            ],
            'is_reference' => [
                'boolean',
            ],
            'is_api' => [
                'boolean',
            ],
            'is_webhook' => [
                'boolean',
            ],
            'is_integration' => [
                'boolean',
            ],
            'is_plugin' => [
                'boolean',
            ],
            'is_extension' => [
                'boolean',
            ],
            'is_addon' => [
                'boolean',
            ],
            'is_module' => [
                'boolean',
            ],
            'is_component' => [
                'boolean',
            ],
            'is_widget' => [
                'boolean',
            ],
            'is_shortcode' => [
                'boolean',
            ],
            'is_block' => [
                'boolean',
            ],
            'is_element' => [
                'boolean',
            ],
            'is_section' => [
                'boolean',
            ],
            'is_partial' => [
                'boolean',
            ],
            'is_layout' => [
                'boolean',
            ],
            'is_theme' => [
                'boolean',
            ],
            'is_skin' => [
                'boolean',
            ],
            'is_style' => [
                'boolean',
            ],
            'is_script' => [
                'boolean',
            ],
            'is_asset' => [
                'boolean',
            ],
            'is_resource' => [
                'boolean',
            ],
            'is_file' => [
                'boolean',
            ],
            'is_folder' => [
                'boolean',
            ],
            'is_directory' => [
                'boolean',
            ],
            'is_path' => [
                'boolean',
            ],
            'is_url' => [
                'boolean',
            ],
            'is_link' => [
                'boolean',
            ],
            'is_redirect' => [
                'boolean',
            ],
            'is_alias' => [
                'boolean',
            ],
            'is_shortcut' => [
                'boolean',
            ],
            'is_bookmark' => [
                'boolean',
            ],
            'is_favorite' => [
                'boolean',
            ],
            'is_starred' => [
                'boolean',
            ],
            'is_pinned' => [
                'boolean',
            ],
            'is_locked' => [
                'boolean',
            ],
            'is_private' => [
                'boolean',
            ],
            'is_public' => [
                'boolean',
            ],
            'is_visible' => [
                'boolean',
            ],
            'is_hidden' => [
                'boolean',
            ],
            'is_disabled' => [
                'boolean',
            ],
            'is_enabled' => [
                'boolean',
            ],
            'is_active' => [
                'boolean',
            ],
            'is_inactive' => [
                'boolean',
            ],
            'is_online' => [
                'boolean',
            ],
            'is_offline' => [
                'boolean',
            ],
            'is_available' => [
                'boolean',
            ],
            'is_unavailable' => [
                'boolean',
            ],
            'is_accessible' => [
                'boolean',
            ],
            'is_inaccessible' => [
                'boolean',
            ],
            'is_editable' => [
                'boolean',
            ],
            'is_readonly' => [
                'boolean',
            ],
            'is_writable' => [
                'boolean',
            ],
            'is_readable' => [
                'boolean',
            ],
            'is_executable' => [
                'boolean',
            ],
            'is_deletable' => [
                'boolean',
            ],
            'is_duplicatable' => [
                'boolean',
            ],
            'is_exportable' => [
                'boolean',
            ],
            'is_importable' => [
                'boolean',
            ],
            'is_exportable_to_csv' => [
                'boolean',
            ],
            'is_exportable_to_excel' => [
                'boolean',
            ],
            'is_exportable_to_pdf' => [
                'boolean',
            ],
            'is_exportable_to_json' => [
                'boolean',
            ],
            'is_exportable_to_xml' => [
                'boolean',
            ],
            'is_exportable_to_yaml' => [
                'boolean',
            ],
            'is_exportable_to_toml' => [
                'boolean',
            ],
            'is_exportable_to_ini' => [
                'boolean',
            ],
            'is_exportable_to_env' => [
                'boolean',
            ],
            'is_exportable_to_dotenv' => [
                'boolean',
            ],
            'is_exportable_to_htaccess' => [
                'boolean',
            ],
            'is_exportable_to_robots' => [
                'boolean',
            ],
            'is_exportable_to_sitemap' => [
                'boolean',
            ],
            'is_exportable_to_manifest' => [
                'boolean',
            ],
            'is_exportable_to_sw' => [
                'boolean',
            ],
            'is_exportable_to_webmanifest' => [
                'boolean',
            ],
            'is_exportable_to_webapp' => [
                'boolean',
            ],
            'is_exportable_to_pwa' => [
                'boolean',
            ],
            'is_exportable_to_spa' => [
                'boolean',
            ],
            'is_exportable_to_ssr' => [
                'boolean',
            ],
            'is_exportable_to_csr' => [
                'boolean',
            ],
            'is_exportable_to_isr' => [
                'boolean',
            ],
            'is_exportable_to_prerender' => [
                'boolean',
            ],
            'is_exportable_to_static' => [
                'boolean',
            ],
            'is_exportable_to_dynamic' => [
                'boolean',
            ],
            'is_exportable_to_hybrid' => [
                'boolean',
            ],
            'is_exportable_to_mixed' => [
                'boolean',
            ],
            'is_exportable_to_other' => [
                'boolean',
            ],
        ], $this->getCommonRules());
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'title.required' => 'Page title is required.',
            'title.min' => 'Page title must be at least 2 characters long.',
            'title.max' => 'Page title must not exceed 255 characters.',
            'slug.required' => 'Page slug is required.',
            'slug.regex' => 'Page slug can only contain lowercase letters, numbers, and hyphens.',
            'slug.unique' => 'This slug is already in use.',
            'slug.min' => 'Page slug must be at least 2 characters long.',
            'slug.max' => 'Page slug must not exceed 100 characters.',
            'content.required' => 'Page content is required.',
            'content.min' => 'Page content must be at least 10 characters long.',
            'content.max' => 'Page content must not exceed 50000 characters.',
            'excerpt.max' => 'Page excerpt must not exceed 500 characters.',
            'status.required' => 'Page status is required.',
            'status.in' => 'Page status must be draft, published, or archived.',
            'featured_image.max' => 'Featured image must not exceed 5MB.',
            'author_id.required' => 'Page author is required.',
            'author_id.exists' => 'Selected author does not exist.',
            'parent_id.exists' => 'Parent page does not exist.',
            'parent_id.different' => 'Page cannot be its own parent.',
            'template.max' => 'Template must not exceed 100 characters.',
            'meta_title.max' => 'Meta title should not exceed 60 characters for SEO.',
            'meta_description.max' => 'Meta description should not exceed 160 characters for SEO.',
        ]);
    }

    /**
     * Additional validation rules.
     */
    protected function additionalValidation($validator): void
    {
        $validator->after(function ($validator): void {
            // Validate title length
            if ($this->filled('title')) {
                $title = $this->title;

                if (mb_strlen($title) < 2) {
                    $validator->errors()->add(
                        'title',
                        'Page title must be at least 2 characters long.'
                    );
                }

                if (mb_strlen($title) > 255) {
                    $validator->errors()->add(
                        'title',
                        'Page title must not exceed 255 characters.'
                    );
                }
            }

            // Validate slug length
            if ($this->filled('slug')) {
                $slug = $this->slug;

                if (mb_strlen($slug) < 2) {
                    $validator->errors()->add(
                        'slug',
                        'Page slug must be at least 2 characters long.'
                    );
                }

                if (mb_strlen($slug) > 100) {
                    $validator->errors()->add(
                        'slug',
                        'Page slug must not exceed 100 characters.'
                    );
                }
            }

            // Validate content
            if ($this->filled('content')) {
                $content = $this->content;

                if (mb_strlen($content) < 10) {
                    $validator->errors()->add(
                        'content',
                        'Page content must be at least 10 characters long.'
                    );
                }

                if (mb_strlen($content) > 50000) {
                    $validator->errors()->add(
                        'content',
                        'Page content must not exceed 50000 characters.'
                    );
                }
            }

            // Validate excerpt
            if ($this->filled('excerpt') && mb_strlen($this->excerpt) > 500) {
                $validator->errors()->add(
                    'excerpt',
                    'Page excerpt must not exceed 500 characters.'
                );
            }

            // Validate status
            if ($this->filled('status')) {
                $status = $this->status;
                $validStatuses = ['draft', 'published', 'archived'];

                if (! in_array($status, $validStatuses)) {
                    $validator->errors()->add(
                        'status',
                        'Invalid page status.'
                    );
                }
            }

            // Validate author
            if ($this->filled('author_id')) {
                $authorId = $this->author_id;
                $author = \Modules\User\Models\User::find($authorId);

                if (! $author) {
                    $validator->errors()->add(
                        'author_id',
                        'Author does not exist.'
                    );
                } elseif (! $author->is_active) {
                    $validator->errors()->add(
                        'author_id',
                        'Author is not active.'
                    );
                }
            }

            // Validate parent page
            if ($this->filled('parent_id')) {
                $parentId = $this->parent_id;
                $pageId = $this->route('page') ?? $this->route('id');

                if ($parentId === $pageId) {
                    $validator->errors()->add(
                        'parent_id',
                        'Page cannot be its own parent.'
                    );
                }

                $parentPage = \Modules\Page\Models\Page::find($parentId);
                if (! $parentPage) {
                    $validator->errors()->add(
                        'parent_id',
                        'Parent page does not exist.'
                    );
                } elseif (! $parentPage->is_active) {
                    $validator->errors()->add(
                        'parent_id',
                        'Parent page is not active.'
                    );
                }
            }

            // Validate template
            if ($this->filled('template') && mb_strlen($this->template) > 100) {
                $validator->errors()->add(
                    'template',
                    'Template must not exceed 100 characters.'
                );
            }

            // Validate meta title
            if ($this->filled('meta_title') && mb_strlen($this->meta_title) > 60) {
                $validator->errors()->add(
                    'meta_title',
                    'Meta title should not exceed 60 characters for SEO.'
                );
            }

            // Validate meta description
            if ($this->filled('meta_description') && mb_strlen($this->meta_description) > 160) {
                $validator->errors()->add(
                    'meta_description',
                    'Meta description should not exceed 160 characters for SEO.'
                );
            }

            // Validate featured image
            if ($this->hasFile('featured_image')) {
                $image = $this->file('featured_image');

                if ($image->getSize() > 5120) { // 5MB
                    $validator->errors()->add(
                        'featured_image',
                        'Featured image must not exceed 5MB.'
                    );
                }
            }

            // Validate page title uniqueness (case insensitive)
            if ($this->filled('title')) {
                $title = $this->title;
                $pageId = $this->route('page') ?? $this->route('id');

                $existingPage = \Modules\Page\Models\Page::where('title', 'LIKE', $title)
                    ->where('id', '!=', $pageId)
                    ->first();

                if ($existingPage) {
                    $validator->errors()->add(
                        'title',
                        'A page with this title already exists.'
                    );
                }
            }

            // Validate page slug uniqueness (case insensitive)
            if ($this->filled('slug')) {
                $slug = $this->slug;
                $pageId = $this->route('page') ?? $this->route('id');

                $existingPage = \Modules\Page\Models\Page::where('slug', 'LIKE', $slug)
                    ->where('id', '!=', $pageId)
                    ->first();

                if ($existingPage) {
                    $validator->errors()->add(
                        'slug',
                        'A page with this slug already exists.'
                    );
                }
            }
        });
    }
}
