<?php

declare(strict_types=1);

namespace Modules\Attribute\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\BaseRequest;

class AttributeRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    /** @return array<string, mixed> */
    public function rules(): array
    {
        $attributeId = $this->route('attribute') ?? $this->route('id');

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
                Rule::unique('attributes', 'slug')->ignore($attributeId),
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'type' => [
                'required',
                'string',
                'in:text,textarea,select,multiselect,radio,checkbox,date,datetime,number,email,url,file,image,color,range',
            ],
            'is_required' => [
                'boolean',
            ],
            'is_filterable' => [
                'boolean',
            ],
            'is_searchable' => [
                'boolean',
            ],
            'is_comparable' => [
                'boolean',
            ],
            'is_visible_on_product' => [
                'boolean',
            ],
            'is_visible_on_listing' => [
                'boolean',
            ],
            'is_visible_on_detail' => [
                'boolean',
            ],
            'is_visible_on_cart' => [
                'boolean',
            ],
            'is_visible_on_checkout' => [
                'boolean',
            ],
            'is_visible_on_order' => [
                'boolean',
            ],
            'is_visible_on_invoice' => [
                'boolean',
            ],
            'is_visible_on_receipt' => [
                'boolean',
            ],
            'is_visible_on_email' => [
                'boolean',
            ],
            'is_visible_on_sms' => [
                'boolean',
            ],
            'is_visible_on_push' => [
                'boolean',
            ],
            'is_visible_on_webhook' => [
                'boolean',
            ],
            'is_visible_on_api' => [
                'boolean',
            ],
            'is_visible_on_admin' => [
                'boolean',
            ],
            'is_visible_on_frontend' => [
                'boolean',
            ],
            'is_visible_on_mobile' => [
                'boolean',
            ],
            'is_visible_on_desktop' => [
                'boolean',
            ],
            'is_visible_on_tablet' => [
                'boolean',
            ],
            'is_visible_on_tv' => [
                'boolean',
            ],
            'is_visible_on_watch' => [
                'boolean',
            ],
            'is_visible_on_other' => [
                'boolean',
            ],
            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
                'max:9999',
            ],
            'validation_rules' => [
                'nullable',
                'array',
            ],
            'validation_rules.*' => [
                'string',
                'max:255',
            ],
            'default_value' => [
                'nullable',
                'string',
                'max:500',
            ],
            'placeholder' => [
                'nullable',
                'string',
                'max:255',
            ],
            'help_text' => [
                'nullable',
                'string',
                'max:500',
            ],
            'error_message' => [
                'nullable',
                'string',
                'max:255',
            ],
            'options' => [
                'nullable',
                'array',
            ],
            'options.*' => [
                'string',
                'max:255',
            ],
            'min_value' => [
                'nullable',
                'numeric',
                'max:999999.99',
            ],
            'max_value' => [
                'nullable',
                'numeric',
                'max:999999.99',
            ],
            'step_value' => [
                'nullable',
                'numeric',
                'min:0.01',
                'max:999999.99',
            ],
            'min_length' => [
                'nullable',
                'integer',
                'min:0',
                'max:9999',
            ],
            'max_length' => [
                'nullable',
                'integer',
                'min:1',
                'max:9999',
            ],
            'min_selections' => [
                'nullable',
                'integer',
                'min:0',
                'max:9999',
            ],
            'max_selections' => [
                'nullable',
                'integer',
                'min:1',
                'max:9999',
            ],
            'file_types' => [
                'nullable',
                'array',
            ],
            'file_types.*' => [
                'string',
                'max:10',
            ],
            'file_size_min' => [
                'nullable',
                'integer',
                'min:0',
                'max:999999',
            ],
            'file_size_max' => [
                'nullable',
                'integer',
                'min:1',
                'max:999999',
            ],
            'image_width_min' => [
                'nullable',
                'integer',
                'min:1',
                'max:9999',
            ],
            'image_width_max' => [
                'nullable',
                'integer',
                'min:1',
                'max:9999',
            ],
            'image_height_min' => [
                'nullable',
                'integer',
                'min:1',
                'max:9999',
            ],
            'image_height_max' => [
                'nullable',
                'integer',
                'max:9999',
            ],
            'color_format' => [
                'nullable',
                'string',
                'in:hex,rgb,rgba,hsl,hsla',
            ],
            'date_format' => [
                'nullable',
                'string',
                'max:50',
            ],
            'time_format' => [
                'nullable',
                'string',
                'in:12,24',
            ],
            'timezone' => [
                'nullable',
                'string',
                'max:50',
            ],
            'locale' => [
                'nullable',
                'string',
                'max:10',
            ],
            'currency' => [
                'nullable',
                'string',
                'size:3',
                'regex:/^[A-Z]{3}$/',
            ],
            'unit' => [
                'nullable',
                'string',
                'max:20',
            ],
            'prefix' => [
                'nullable',
                'string',
                'max:20',
            ],
            'suffix' => [
                'nullable',
                'string',
                'max:20',
            ],
            'is_active' => [
                'boolean',
            ],
            'is_system' => [
                'boolean',
            ],
            'is_custom' => [
                'boolean',
            ],
            'is_global' => [
                'boolean',
            ],
            'is_local' => [
                'boolean',
            ],
            'is_inherited' => [
                'boolean',
            ],
            'is_overridden' => [
                'boolean',
            ],
            'is_locked' => [
                'boolean',
            ],
            'is_editable' => [
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

    // Note: BaseRequest::messages() is final, so we can't override it.
    // Custom messages are handled via the base class messages() method.

    /**
     * Additional validation rules.
     */
    protected function additionalValidation(\Illuminate\Contracts\Validation\Validator $validator): void
    {
        $validator->after(function ($validator): void {
            // Validate name length
            if ($this->filled('name')) {
                $name = $this->name;

                if (mb_strlen($name) < 2) {
                    $validator->errors()->add(
                        'name',
                        'Attribute name must be at least 2 characters long.'
                    );
                }

                if (mb_strlen($name) > 255) {
                    $validator->errors()->add(
                        'name',
                        'Attribute name must not exceed 255 characters.'
                    );
                }
            }

            // Validate slug length
            if ($this->filled('slug')) {
                $slug = $this->slug;

                if (mb_strlen($slug) < 2) {
                    $validator->errors()->add(
                        'slug',
                        'Attribute slug must be at least 2 characters long.'
                    );
                }

                if (mb_strlen($slug) > 100) {
                    $validator->errors()->add(
                        'slug',
                        'Attribute slug must not exceed 100 characters.'
                    );
                }
            }

            // Validate description
            if ($this->filled('description') && mb_strlen($this->description) > 1000) {
                $validator->errors()->add(
                    'description',
                    'Description must not exceed 1000 characters.'
                );
            }

            // Validate type
            if ($this->filled('type')) {
                $type = $this->type;
                $validTypes = [
                    'text', 'textarea', 'select', 'multiselect', 'radio', 'checkbox',
                    'date', 'datetime', 'number', 'email', 'url', 'file', 'image',
                    'color', 'range',
                ];

                if (! in_array($type, $validTypes)) {
                    $validator->errors()->add(
                        'type',
                        'Invalid attribute type.'
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

            // Validate validation rules
            if ($this->filled('validation_rules')) {
                $validationRules = $this->validation_rules;

                if (count($validationRules) > 50) {
                    $validator->errors()->add(
                        'validation_rules',
                        'Maximum 50 validation rules are allowed.'
                    );
                }

                foreach ($validationRules as $index => $rule) {
                    if (mb_strlen($rule) < 2) {
                        $validator->errors()->add(
                            'validation_rules.'.$index,
                            'Each validation rule must be at least 2 characters long.'
                        );
                    }
                }
            }

            // Validate default value
            if ($this->filled('default_value') && mb_strlen($this->default_value) > 500) {
                $validator->errors()->add(
                    'default_value',
                    'Default value must not exceed 500 characters.'
                );
            }

            // Validate placeholder
            if ($this->filled('placeholder') && mb_strlen($this->placeholder) > 255) {
                $validator->errors()->add(
                    'placeholder',
                    'Placeholder must not exceed 255 characters.'
                );
            }

            // Validate help text
            if ($this->filled('help_text') && mb_strlen($this->help_text) > 500) {
                $validator->errors()->add(
                    'help_text',
                    'Help text must not exceed 500 characters.'
                );
            }

            // Validate error message
            if ($this->filled('error_message') && mb_strlen($this->error_message) > 255) {
                $validator->errors()->add(
                    'error_message',
                    'Error message must not exceed 255 characters.'
                );
            }

            // Validate options
            if ($this->filled('options')) {
                $options = $this->options;

                if (count($options) > 100) {
                    $validator->errors()->add(
                        'options',
                        'Maximum 100 options are allowed.'
                    );
                }

                foreach ($options as $index => $option) {
                    if (mb_strlen($option) < 1) {
                        $validator->errors()->add(
                            'options.'.$index,
                            'Each option must be at least 1 character long.'
                        );
                    }

                    if (mb_strlen($option) > 255) {
                        $validator->errors()->add(
                            'options.'.$index,
                            'Each option must not exceed 255 characters.'
                        );
                    }
                }
            }

            // Validate min/max values
            if ($this->filled('min_value') && $this->filled('max_value')) {
                $minValue = $this->min_value;
                $maxValue = $this->max_value;

                if ($minValue >= $maxValue) {
                    $validator->errors()->add(
                        'max_value',
                        'Maximum value must be greater than minimum value.'
                    );
                }
            }

            // Validate step value
            if ($this->filled('step_value')) {
                $stepValue = $this->step_value;

                if ($stepValue <= 0) {
                    $validator->errors()->add(
                        'step_value',
                        'Step value must be greater than 0.'
                    );
                }
            }

            // Validate min/max length
            if ($this->filled('min_length') && $this->filled('max_length')) {
                $minLength = $this->min_length;
                $maxLength = $this->max_length;

                if ($minLength >= $maxLength) {
                    $validator->errors()->add(
                        'max_length',
                        'Maximum length must be greater than minimum length.'
                    );
                }
            }

            // Validate min/max selections
            if ($this->filled('min_selections') && $this->filled('max_selections')) {
                $minSelections = $this->min_selections;
                $maxSelections = $this->max_selections;

                if ($minSelections >= $maxSelections) {
                    $validator->errors()->add(
                        'max_selections',
                        'Maximum selections must be greater than minimum selections.'
                    );
                }
            }

            // Validate file types
            if ($this->filled('file_types')) {
                $fileTypes = $this->file_types;

                if (count($fileTypes) > 20) {
                    $validator->errors()->add(
                        'file_types',
                        'Maximum 20 file types are allowed.'
                    );
                }

                $validFileTypes = [
                    'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'tiff',
                    'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
                    'txt', 'rtf', 'odt', 'ods', 'odp', 'zip', 'rar', '7z',
                    'mp3', 'mp4', 'avi', 'mov', 'wmv', 'flv', 'webm',
                    'css', 'js', 'html', 'htm', 'xml', 'json', 'yaml', 'yml',
                ];

                foreach ($fileTypes as $index => $fileType) {
                    if (! in_array(mb_strtolower($fileType), $validFileTypes)) {
                        $validator->errors()->add(
                            'file_types.'.$index,
                            'Invalid file type: '.$fileType
                        );
                    }
                }
            }

            // Validate file size limits
            if ($this->filled('file_size_min') && $this->filled('file_size_max')) {
                $fileSizeMin = $this->file_size_min;
                $fileSizeMax = $this->file_size_max;

                if ($fileSizeMin >= $fileSizeMax) {
                    $validator->errors()->add(
                        'file_size_max',
                        'Maximum file size must be greater than minimum file size.'
                    );
                }
            }

            // Validate image dimensions
            if ($this->filled('image_width_min') && $this->filled('image_width_max')) {
                $imageWidthMin = $this->image_width_min;
                $imageWidthMax = $this->image_width_max;

                if ($imageWidthMin >= $imageWidthMax) {
                    $validator->errors()->add(
                        'image_width_max',
                        'Maximum image width must be greater than minimum image width.'
                    );
                }
            }

            if ($this->filled('image_height_min') && $this->filled('image_height_max')) {
                $imageHeightMin = $this->image_height_min;
                $imageHeightMax = $this->image_height_max;

                if ($imageHeightMin >= $imageHeightMax) {
                    $validator->errors()->add(
                        'image_height_max',
                        'Maximum image height must be greater than minimum image height.'
                    );
                }
            }

            // Validate color format
            if ($this->filled('color_format')) {
                $colorFormat = $this->color_format;
                $validFormats = ['hex', 'rgb', 'rgba', 'hsl', 'hsla'];

                if (! in_array($colorFormat, $validFormats)) {
                    $validator->errors()->add(
                        'color_format',
                        'Invalid color format.'
                    );
                }
            }

            // Validate date format
            if ($this->filled('date_format') && mb_strlen($this->date_format) > 50) {
                $validator->errors()->add(
                    'date_format',
                    'Date format must not exceed 50 characters.'
                );
            }

            // Validate time format
            if ($this->filled('time_format')) {
                $timeFormat = $this->time_format;
                $validFormats = ['12', '24'];

                if (! in_array($timeFormat, $validFormats)) {
                    $validator->errors()->add(
                        'time_format',
                        'Time format must be 12 or 24.'
                    );
                }
            }

            // Validate timezone
            if ($this->filled('timezone') && mb_strlen($this->timezone) > 50) {
                $validator->errors()->add(
                    'timezone',
                    'Timezone must not exceed 50 characters.'
                );
            }

            // Validate locale
            if ($this->filled('locale') && $this->locale && mb_strlen($this->locale) > 10) {
                $validator->errors()->add(
                    'locale',
                    'Locale must not exceed 10 characters.'
                );
            }

            // Validate currency
            if ($this->filled('currency')) {
                $currency = $this->currency;
                $validCurrencies = ['USD', 'EUR', 'GBP', 'JPY', 'AUD', 'CAD', 'CHF', 'CNY', 'SEK', 'NZD'];

                if (! in_array($currency, $validCurrencies)) {
                    $validator->errors()->add(
                        'currency',
                        'Invalid currency code.'
                    );
                }
            }

            // Validate unit
            if ($this->filled('unit') && mb_strlen($this->unit) > 20) {
                $validator->errors()->add(
                    'unit',
                    'Unit must not exceed 20 characters.'
                );
            }

            // Validate prefix
            if ($this->filled('prefix') && mb_strlen($this->prefix) > 20) {
                $validator->errors()->add(
                    'prefix',
                    'Prefix must not exceed 20 characters.'
                );
            }

            // Validate suffix
            if ($this->filled('suffix') && mb_strlen($this->suffix) > 20) {
                $validator->errors()->add(
                    'suffix',
                    'Suffix must not exceed 20 characters.'
                );
            }

            // Validate attribute name uniqueness (case insensitive)
            if ($this->filled('name')) {
                $name = $this->name;
                $attributeId = $this->route('attribute') ?? $this->route('id');

                $existingAttribute = \Modules\Attribute\Models\Attribute::where('name', 'LIKE', $name)
                    ->where('id', '!=', $attributeId)
                    ->first();

                if ($existingAttribute) {
                    $validator->errors()->add(
                        'name',
                        'A attribute with this name already exists.'
                    );
                }
            }

            // Validate attribute slug uniqueness (case insensitive)
            if ($this->filled('slug')) {
                $slug = $this->slug;
                $attributeId = $this->route('attribute') ?? $this->route('id');

                $existingAttribute = \Modules\Attribute\Models\Attribute::where('slug', 'LIKE', $slug)
                    ->where('id', '!=', $attributeId)
                    ->first();

                if ($existingAttribute) {
                    $validator->errors()->add(
                        'slug',
                        'A attribute with this slug already exists.'
                    );
                }
            }
        });
    }
}
