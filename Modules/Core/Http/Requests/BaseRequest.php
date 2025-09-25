<?php

declare(strict_types=1);

namespace Modules\Core\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class BaseRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    abstract public function rules(): array;

    /**
     * Determine if the user is authorized to make this request.
     */
    final public function authorize(): bool
    {
        return true;
    }

    /**
     * Get custom messages for validator errors.
     */
    final public function messages(): array
    {
        return [
            'required' => 'The :attribute field is required.',
            'email' => 'The :attribute must be a valid email address.',
            'unique' => 'The :attribute has already been taken.',
            'min' => 'The :attribute must be at least :min characters.',
            'max' => 'The :attribute may not be greater than :max characters.',
            'numeric' => 'The :attribute must be a number.',
            'integer' => 'The :attribute must be an integer.',
            'boolean' => 'The :attribute field must be true or false.',
            'date' => 'The :attribute is not a valid date.',
            'image' => 'The :attribute must be an image.',
            'mimes' => 'The :attribute must be a file of type: :values.',
            'regex' => 'The :attribute format is invalid.',
            'confirmed' => 'The :attribute confirmation does not match.',
            'same' => 'The :attribute and :other must match.',
            'different' => 'The :attribute and :other must be different.',
            'digits' => 'The :attribute must be :digits digits.',
            'digits_between' => 'The :attribute must be between :min and :max digits.',
            'size' => 'The :attribute must be :size.',
            'between' => 'The :attribute must be between :min and :max.',
            'in' => 'The selected :attribute is invalid.',
            'not_in' => 'The selected :attribute is invalid.',
            'exists' => 'The selected :attribute is invalid.',
            'file' => 'The :attribute must be a file.',
            'filled' => 'The :attribute field must have a value.',
            'present' => 'The :attribute field must be present.',
            'required_if' => 'The :attribute field is required when :other is :value.',
            'required_unless' => 'The :attribute field is required unless :other is in :values.',
            'required_with' => 'The :attribute field is required when :values is present.',
            'required_with_all' => 'The :attribute field is required when :values are present.',
            'required_without' => 'The :attribute field is required when :values is not present.',
            'required_without_all' => 'The :attribute field is required when none of :values are present.',
            'accepted' => 'The :attribute must be accepted.',
            'active_url' => 'The :attribute is not a valid URL.',
            'after' => 'The :attribute must be a date after :date.',
            'after_or_equal' => 'The :attribute must be a date after or equal to :date.',
            'alpha' => 'The :attribute may only contain letters.',
            'alpha_dash' => 'The :attribute may only contain letters, numbers, dashes and underscores.',
            'alpha_num' => 'The :attribute may only contain letters and numbers.',
            'array' => 'The :attribute must be an array.',
            'before' => 'The :attribute must be a date before :date.',
            'before_or_equal' => 'The :attribute must be a date before or equal to :date.',
            'json' => 'The :attribute must be a valid JSON string.',
            'nullable' => 'The :attribute may be null.',
            'string' => 'The :attribute must be a string.',
            'timezone' => 'The :attribute must be a valid zone.',
            'url' => 'The :attribute format is invalid.',
            'uuid' => 'The :attribute must be a valid UUID.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    final public function attributes(): array
    {
        return [
            'email' => 'email address',
            'password' => 'password',
            'password_confirmation' => 'password confirmation',
            'name' => 'name',
            'title' => 'title',
            'description' => 'description',
            'price' => 'price',
            'quantity' => 'quantity',
            'status' => 'status',
            'slug' => 'slug',
            'summary' => 'summary',
            'sku' => 'SKU',
            'stock' => 'stock',
            'special_price' => 'special price',
            'special_price_start' => 'special price start date',
            'special_price_end' => 'special price end date',
            'is_featured' => 'featured status',
            'd_deal' => 'deal status',
            'brand_id' => 'brand',
            'category_id' => 'category',
            'images' => 'images',
            'meta_title' => 'meta title',
            'meta_description' => 'meta description',
            'meta_keywords' => 'meta keywords',
            'phone' => 'phone number',
            'address' => 'address',
            'city' => 'city',
            'country' => 'country',
            'postal_code' => 'postal code',
            'date_of_birth' => 'date of birth',
            'gender' => 'gender',
            'newsletter_subscription' => 'newsletter subscription',
            'marketing_emails' => 'marketing emails',
        ];
    }

    /**
     * Configure the validator instance.
     */
    final public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator): void {
            $this->additionalValidation($validator);
        });
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator): void
    {
        if ($this->expectsJson()) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                    'error_code' => 'VALIDATION_ERROR',
                ], 422)
            );
        }

        parent::failedValidation($validator);
    }

    /**
     * Additional validation rules that can be overridden in child classes.
     */
    protected function additionalValidation(Validator $validator): void
    {
        // Override in child classes for additional validation
    }

    /**
     * Sanitize input data.
     */
    protected function sanitizeInput(): array
    {
        $data = $this->all();

        // Remove HTML tags from string fields
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = strip_tags($value);
            }
        }

        return $data;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge($this->sanitizeInput());
    }

    /**
     * Get validation rules for common fields
     */
    protected function getCommonRules(): array
    {
        return [
            'status' => [
                'nullable',
                'string',
                'in:active,inactive,draft,pending,approved,rejected',
            ],
            'created_at' => [
                'nullable',
                'date',
            ],
            'updated_at' => [
                'nullable',
                'date',
            ],
        ];
    }

    /**
     * Get validation rules for pagination
     */
    protected function getPaginationRules(): array
    {
        return [
            'page' => [
                'nullable',
                'integer',
                'min:1',
            ],
            'per_page' => [
                'nullable',
                'integer',
                'min:1',
                'max:100',
            ],
            'sort_by' => [
                'nullable',
                'string',
                'max:50',
            ],
            'sort_direction' => [
                'nullable',
                'string',
                'in:asc,desc',
            ],
        ];
    }

    /**
     * Get validation rules for search
     */
    protected function getSearchRules(): array
    {
        return [
            'search' => [
                'nullable',
                'string',
                'max:255',
            ],
            'q' => [
                'nullable',
                'string',
                'max:255',
            ],
            'filter' => [
                'nullable',
                'array',
            ],
            'filter.*' => [
                'string',
                'max:255',
            ],
        ];
    }

    /**
     * Get validation rules for date range
     */
    protected function getDateRangeRules(): array
    {
        return [
            'start_date' => [
                'nullable',
                'date',
                'before_or_equal:end_date',
            ],
            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],
            'date_from' => [
                'nullable',
                'date',
                'before_or_equal:date_to',
            ],
            'date_to' => [
                'nullable',
                'date',
                'after_or_equal:date_from',
            ],
        ];
    }

    /**
     * Get validation rules for file uploads
     */
    protected function getFileUploadRules(): array
    {
        return [
            'file' => [
                'nullable',
                'file',
                'max:10240', // 10MB
            ],
            'image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:5120', // 5MB
            ],
            'document' => [
                'nullable',
                'file',
                'mimes:pdf,doc,docx,txt',
                'max:10240', // 10MB
            ],
        ];
    }

    /**
     * Get validation rules for API authentication
     */
    protected function getApiAuthRules(): array
    {
        return [
            'api_token' => [
                'nullable',
                'string',
                'size:80',
            ],
            'bearer_token' => [
                'nullable',
                'string',
                'min:10',
            ],
        ];
    }
}
