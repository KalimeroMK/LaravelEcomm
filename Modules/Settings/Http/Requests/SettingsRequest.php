<?php

declare(strict_types=1);

namespace Modules\Settings\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;

class SettingsRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return array_merge([
            'site_name' => [
                'required',
                'string',
                'max:255',
            ],
            'site_description' => [
                'nullable',
                'string',
                'max:500',
            ],
            'site_keywords' => [
                'nullable',
                'string',
                'max:255',
            ],
            'site_logo' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp,svg',
                'max:2048',
            ],
            'site_favicon' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp,ico',
                'max:1024',
            ],
            'admin_email' => [
                'required',
                'email:rfc,dns',
                'max:255',
            ],
            'support_email' => [
                'nullable',
                'email:rfc,dns',
                'max:255',
            ],
            'contact_email' => [
                'nullable',
                'email:rfc,dns',
                'max:255',
            ],
            'contact_phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[\+]?[1-9][\d]{0,15}$/',
            ],
            'contact_address' => [
                'nullable',
                'string',
                'max:500',
            ],
            'contact_city' => [
                'nullable',
                'string',
                'max:100',
            ],
            'contact_country' => [
                'nullable',
                'string',
                'max:100',
            ],
            'contact_postal_code' => [
                'nullable',
                'string',
                'max:20',
            ],
            'currency' => [
                'required',
                'string',
                'size:3',
                'regex:/^[A-Z]{3}$/',
            ],
            'currency_symbol' => [
                'required',
                'string',
                'max:10',
            ],
            'timezone' => [
                'required',
                'string',
                'max:50',
            ],
            'date_format' => [
                'required',
                'string',
                'max:20',
            ],
            'time_format' => [
                'required',
                'string',
                'in:12,24',
            ],
            'language' => [
                'required',
                'string',
                'size:2',
                'regex:/^[a-z]{2}$/',
            ],
            'maintenance_mode' => [
                'boolean',
            ],
            'maintenance_message' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'registration_enabled' => [
                'boolean',
            ],
            'email_verification_required' => [
                'boolean',
            ],
            'password_reset_enabled' => [
                'boolean',
            ],
            'two_factor_enabled' => [
                'boolean',
            ],
            'social_login_enabled' => [
                'boolean',
            ],
            'google_client_id' => [
                'nullable',
                'string',
                'max:255',
            ],
            'google_client_secret' => [
                'nullable',
                'string',
                'max:255',
            ],
            'facebook_app_id' => [
                'nullable',
                'string',
                'max:255',
            ],
            'facebook_app_secret' => [
                'nullable',
                'string',
                'max:255',
            ],
            'twitter_client_id' => [
                'nullable',
                'string',
                'max:255',
            ],
            'twitter_client_secret' => [
                'nullable',
                'string',
                'max:255',
            ],
            'linkedin_client_id' => [
                'nullable',
                'string',
                'max:255',
            ],
            'linkedin_client_secret' => [
                'nullable',
                'string',
                'max:255',
            ],
            'github_client_id' => [
                'nullable',
                'string',
                'max:255',
            ],
            'github_client_secret' => [
                'nullable',
                'string',
                'max:255',
            ],
            'stripe_public_key' => [
                'nullable',
                'string',
                'max:255',
            ],
            'stripe_secret_key' => [
                'nullable',
                'string',
                'max:255',
            ],
            'stripe_webhook_secret' => [
                'nullable',
                'string',
                'max:255',
            ],
            'paypal_client_id' => [
                'nullable',
                'string',
                'max:255',
            ],
            'paypal_client_secret' => [
                'nullable',
                'string',
                'max:255',
            ],
            'paypal_mode' => [
                'nullable',
                'string',
                'in:sandbox,live',
            ],
            'smtp_host' => [
                'nullable',
                'string',
                'max:255',
            ],
            'smtp_port' => [
                'nullable',
                'integer',
                'min:1',
                'max:65535',
            ],
            'smtp_username' => [
                'nullable',
                'string',
                'max:255',
            ],
            'smtp_password' => [
                'nullable',
                'string',
                'max:255',
            ],
            'smtp_encryption' => [
                'nullable',
                'string',
                'in:tls,ssl',
            ],
            'smtp_from_address' => [
                'nullable',
                'email:rfc,dns',
                'max:255',
            ],
            'smtp_from_name' => [
                'nullable',
                'string',
                'max:255',
            ],
            'cache_driver' => [
                'nullable',
                'string',
                'in:file,database,redis,memcached',
            ],
            'queue_driver' => [
                'nullable',
                'string',
                'in:sync,database,redis,sqs',
            ],
            'session_driver' => [
                'nullable',
                'string',
                'in:file,database,redis,memcached',
            ],
            'file_driver' => [
                'nullable',
                'string',
                'in:local,s3,ftp,ftp',
            ],
            'aws_access_key_id' => [
                'nullable',
                'string',
                'max:255',
            ],
            'aws_secret_access_key' => [
                'nullable',
                'string',
                'max:255',
            ],
            'aws_default_region' => [
                'nullable',
                'string',
                'max:50',
            ],
            'aws_bucket' => [
                'nullable',
                'string',
                'max:255',
            ],
            'aws_url' => [
                'nullable',
                'url',
                'max:500',
            ],
            'aws_endpoint' => [
                'nullable',
                'url',
                'max:500',
            ],
            'aws_use_path_style_endpoint' => [
                'boolean',
            ],
            'aws_throw_exception_on_failure' => [
                'boolean',
            ],
            'aws_visibility' => [
                'nullable',
                'string',
                'in:public,private',
            ],
            'aws_disk' => [
                'nullable',
                'string',
                'max:50',
            ],
            'aws_root' => [
                'nullable',
                'string',
                'max:255',
            ],
            'aws_options' => [
                'nullable',
                'array',
            ],
            'aws_options.*' => [
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
            'site_name.required' => 'Site name is required.',
            'site_name.max' => 'Site name must not exceed 255 characters.',
            'site_description.max' => 'Site description must not exceed 500 characters.',
            'site_keywords.max' => 'Site keywords must not exceed 255 characters.',
            'site_logo.max' => 'Site logo must not be larger than 2MB.',
            'site_favicon.max' => 'Site favicon must not be larger than 1MB.',
            'admin_email.required' => 'Admin email is required.',
            'admin_email.email' => 'Please enter a valid admin email address.',
            'support_email.email' => 'Please enter a valid support email address.',
            'contact_email.email' => 'Please enter a valid contact email address.',
            'contact_phone.regex' => 'Please enter a valid contact phone number.',
            'contact_phone.max' => 'Contact phone must not exceed 20 characters.',
            'contact_address.max' => 'Contact address must not exceed 500 characters.',
            'contact_city.max' => 'Contact city must not exceed 100 characters.',
            'contact_country.max' => 'Contact country must not exceed 100 characters.',
            'contact_postal_code.max' => 'Contact postal code must not exceed 20 characters.',
            'currency.required' => 'Currency is required.',
            'currency.size' => 'Currency must be exactly 3 characters.',
            'currency.regex' => 'Currency must be in uppercase format (e.g., USD, EUR).',
            'currency_symbol.required' => 'Currency symbol is required.',
            'currency_symbol.max' => 'Currency symbol must not exceed 10 characters.',
            'timezone.required' => 'Timezone is required.',
            'timezone.max' => 'Timezone must not exceed 50 characters.',
            'date_format.required' => 'Date format is required.',
            'date_format.max' => 'Date format must not exceed 20 characters.',
            'time_format.required' => 'Time format is required.',
            'time_format.in' => 'Time format must be 12 or 24.',
            'language.required' => 'Language is required.',
            'language.size' => 'Language must be exactly 2 characters.',
            'language.regex' => 'Language must be in lowercase format (e.g., en, es, fr).',
            'maintenance_message.max' => 'Maintenance message must not exceed 1000 characters.',
            'smtp_port.min' => 'SMTP port must be at least 1.',
            'smtp_port.max' => 'SMTP port cannot exceed 65535.',
            'smtp_encryption.in' => 'SMTP encryption must be tls or ssl.',
            'smtp_from_address.email' => 'Please enter a valid SMTP from email address.',
            'smtp_from_name.max' => 'SMTP from name must not exceed 255 characters.',
            'cache_driver.in' => 'Invalid cache driver.',
            'queue_driver.in' => 'Invalid queue driver.',
            'session_driver.in' => 'Invalid session driver.',
            'file_driver.in' => 'Invalid file driver.',
            'aws_default_region.max' => 'AWS default region must not exceed 50 characters.',
            'aws_bucket.max' => 'AWS bucket must not exceed 255 characters.',
            'aws_url.url' => 'Please enter a valid AWS URL.',
            'aws_url.max' => 'AWS URL must not exceed 500 characters.',
            'aws_endpoint.url' => 'Please enter a valid AWS endpoint.',
            'aws_endpoint.max' => 'AWS endpoint must not exceed 500 characters.',
            'aws_visibility.in' => 'AWS visibility must be public or private.',
            'aws_disk.max' => 'AWS disk must not exceed 50 characters.',
            'aws_root.max' => 'AWS root must not exceed 255 characters.',
            'aws_options.*.max' => 'AWS options must not exceed 255 characters.',
        ]);
    }

    /**
     * Additional validation rules.
     */
    protected function additionalValidation($validator): void
    {
        $validator->after(function ($validator): void {
            // Validate site name
            if ($this->filled('site_name') && mb_strlen($this->site_name) < 3) {
                $validator->errors()->add(
                    'site_name',
                    'Site name must be at least 3 characters long.'
                );
            }

            // Validate site description
            if ($this->filled('site_description') && mb_strlen($this->site_description) > 500) {
                $validator->errors()->add(
                    'site_description',
                    'Site description must not exceed 500 characters.'
                );
            }

            // Validate site keywords
            if ($this->filled('site_keywords') && mb_strlen($this->site_keywords) > 255) {
                $validator->errors()->add(
                    'site_keywords',
                    'Site keywords must not exceed 255 characters.'
                );
            }

            // Validate admin email
            if ($this->filled('admin_email')) {
                $adminEmail = $this->admin_email;

                if (! filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
                    $validator->errors()->add(
                        'admin_email',
                        'Please enter a valid admin email address.'
                    );
                }
            }

            // Validate support email
            if ($this->filled('support_email')) {
                $supportEmail = $this->support_email;

                if (! filter_var($supportEmail, FILTER_VALIDATE_EMAIL)) {
                    $validator->errors()->add(
                        'support_email',
                        'Please enter a valid support email address.'
                    );
                }
            }

            // Validate contact email
            if ($this->filled('contact_email')) {
                $contactEmail = $this->contact_email;

                if (! filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) {
                    $validator->errors()->add(
                        'contact_email',
                        'Please enter a valid contact email address.'
                    );
                }
            }

            // Validate contact phone
            if ($this->filled('contact_phone')) {
                $contactPhone = preg_replace('/[^0-9+]/', '', $this->contact_phone);

                if (mb_strlen($contactPhone) < 10 || mb_strlen($contactPhone) > 15) {
                    $validator->errors()->add(
                        'contact_phone',
                        'Contact phone must be between 10 and 15 digits.'
                    );
                }
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

            // Validate timezone
            if ($this->filled('timezone')) {
                $timezone = $this->timezone;

                if (! in_array($timezone, timezone_identifiers_list())) {
                    $validator->errors()->add(
                        'timezone',
                        'Invalid timezone.'
                    );
                }
            }

            // Validate date format
            if ($this->filled('date_format')) {
                $dateFormat = $this->date_format;
                $validFormats = ['Y-m-d', 'd-m-Y', 'm/d/Y', 'd/m/Y', 'Y/m/d', 'm-d-Y'];

                if (! in_array($dateFormat, $validFormats)) {
                    $validator->errors()->add(
                        'date_format',
                        'Invalid date format.'
                    );
                }
            }

            // Validate language
            if ($this->filled('language')) {
                $language = $this->language;
                $validLanguages = ['en', 'es', 'fr', 'de', 'it', 'pt', 'ru', 'ja', 'ko', 'zh', 'ar', 'hi'];

                if (! in_array($language, $validLanguages)) {
                    $validator->errors()->add(
                        'language',
                        'Invalid language code.'
                    );
                }
            }

            // Validate maintenance message
            if ($this->filled('maintenance_message') && mb_strlen($this->maintenance_message) > 1000) {
                $validator->errors()->add(
                    'maintenance_message',
                    'Maintenance message must not exceed 1000 characters.'
                );
            }

            // Validate SMTP settings
            if ($this->filled('smtp_host') || $this->filled('smtp_port') || $this->filled('smtp_username') || $this->filled('smtp_password')) {
                if (! $this->filled('smtp_host')) {
                    $validator->errors()->add(
                        'smtp_host',
                        'SMTP host is required when configuring SMTP settings.'
                    );
                }

                if (! $this->filled('smtp_port')) {
                    $validator->errors()->add(
                        'smtp_port',
                        'SMTP port is required when configuring SMTP settings.'
                    );
                }

                if (! $this->filled('smtp_username')) {
                    $validator->errors()->add(
                        'smtp_username',
                        'SMTP username is required when configuring SMTP settings.'
                    );
                }

                if (! $this->filled('smtp_password')) {
                    $validator->errors()->add(
                        'smtp_password',
                        'SMTP password is required when configuring SMTP settings.'
                    );
                }
            }

            // Validate AWS settings
            if ($this->filled('aws_access_key_id') || $this->filled('aws_secret_access_key') || $this->filled('aws_default_region') || $this->filled('aws_bucket')) {
                if (! $this->filled('aws_access_key_id')) {
                    $validator->errors()->add(
                        'aws_access_key_id',
                        'AWS Access Key ID is required when configuring AWS settings.'
                    );
                }

                if (! $this->filled('aws_secret_access_key')) {
                    $validator->errors()->add(
                        'aws_secret_access_key',
                        'AWS Secret Access Key is required when configuring AWS settings.'
                    );
                }

                if (! $this->filled('aws_default_region')) {
                    $validator->errors()->add(
                        'aws_default_region',
                        'AWS Default Region is required when configuring AWS settings.'
                    );
                }

                if (! $this->filled('aws_bucket')) {
                    $validator->errors()->add(
                        'aws_bucket',
                        'AWS Bucket is required when configuring AWS settings.'
                    );
                }
            }

            // Validate social login settings
            if ($this->filled('google_client_id') || $this->filled('google_client_secret')) {
                if (! $this->filled('google_client_id')) {
                    $validator->errors()->add(
                        'google_client_id',
                        'Google Client ID is required when configuring Google login.'
                    );
                }

                if (! $this->filled('google_client_secret')) {
                    $validator->errors()->add(
                        'google_client_secret',
                        'Google Client Secret is required when configuring Google login.'
                    );
                }
            }

            if ($this->filled('facebook_app_id') || $this->filled('facebook_app_secret')) {
                if (! $this->filled('facebook_app_id')) {
                    $validator->errors()->add(
                        'facebook_app_id',
                        'Facebook App ID is required when configuring Facebook login.'
                    );
                }

                if (! $this->filled('facebook_app_secret')) {
                    $validator->errors()->add(
                        'facebook_app_secret',
                        'Facebook App Secret is required when configuring Facebook login.'
                    );
                }
            }

            // Validate payment settings
            if ($this->filled('stripe_public_key') || $this->filled('stripe_secret_key')) {
                if (! $this->filled('stripe_public_key')) {
                    $validator->errors()->add(
                        'stripe_public_key',
                        'Stripe Public Key is required when configuring Stripe payments.'
                    );
                }

                if (! $this->filled('stripe_secret_key')) {
                    $validator->errors()->add(
                        'stripe_secret_key',
                        'Stripe Secret Key is required when configuring Stripe payments.'
                    );
                }
            }

            if ($this->filled('paypal_client_id') || $this->filled('paypal_client_secret')) {
                if (! $this->filled('paypal_client_id')) {
                    $validator->errors()->add(
                        'paypal_client_id',
                        'PayPal Client ID is required when configuring PayPal payments.'
                    );
                }

                if (! $this->filled('paypal_client_secret')) {
                    $validator->errors()->add(
                        'paypal_client_secret',
                        'PayPal Client Secret is required when configuring PayPal payments.'
                    );
                }
            }
        });
    }
}
