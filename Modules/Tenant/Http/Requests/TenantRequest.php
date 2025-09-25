<?php

declare(strict_types=1);

namespace Modules\Tenant\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\BaseRequest;

class TenantRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $tenantId = $this->route('tenant') ?? $this->route('id');

        return array_merge([
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-_\.]+$/',
            ],
            'domain' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\-\.]+$/',
                Rule::unique('tenants', 'domain')->ignore($tenantId),
            ],
            'subdomain' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9\-]+$/',
                Rule::unique('tenants', 'subdomain')->ignore($tenantId),
            ],
            'database_name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9_]+$/',
                Rule::unique('tenants', 'database_name')->ignore($tenantId),
            ],
            'database_connection' => [
                'required',
                'string',
                'max:50',
            ],
            'status' => [
                'required',
                'string',
                'in:active,inactive,suspended,maintenance',
            ],
            'plan' => [
                'required',
                'string',
                'in:free,basic,premium,enterprise',
            ],
            'max_users' => [
                'nullable',
                'integer',
                'min:1',
                'max:999999',
            ],
            'max_storage' => [
                'nullable',
                'integer',
                'min:1',
                'max:999999',
            ],
            'max_bandwidth' => [
                'nullable',
                'integer',
                'min:1',
                'max:999999',
            ],
            'features' => [
                'nullable',
                'array',
            ],
            'features.*' => [
                'string',
                'max:100',
            ],
            'settings' => [
                'nullable',
                'array',
            ],
            'settings.*' => [
                'string',
                'max:255',
            ],
            'owner_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],
            'billing_email' => [
                'nullable',
                'email:rfc,dns',
                'max:255',
            ],
            'billing_phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[\+]?[1-9][\d]{0,15}$/',
            ],
            'billing_address' => [
                'nullable',
                'string',
                'max:500',
            ],
            'billing_city' => [
                'nullable',
                'string',
                'max:100',
            ],
            'billing_country' => [
                'nullable',
                'string',
                'max:100',
            ],
            'billing_postal_code' => [
                'nullable',
                'string',
                'max:20',
            ],
            'trial_ends_at' => [
                'nullable',
                'date',
                'after:now',
            ],
            'subscription_ends_at' => [
                'nullable',
                'date',
                'after:now',
            ],
            'last_activity_at' => [
                'nullable',
                'date',
                'before_or_equal:now',
            ],
        ], $this->getCommonRules());
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'name.required' => 'Tenant name is required.',
            'name.regex' => 'Tenant name can only contain letters, numbers, spaces, hyphens, underscores, and dots.',
            'name.max' => 'Tenant name must not exceed 255 characters.',
            'domain.required' => 'Domain is required.',
            'domain.regex' => 'Domain can only contain letters, numbers, hyphens, and dots.',
            'domain.unique' => 'This domain is already in use.',
            'domain.max' => 'Domain must not exceed 255 characters.',
            'subdomain.regex' => 'Subdomain can only contain letters, numbers, and hyphens.',
            'subdomain.unique' => 'This subdomain is already in use.',
            'subdomain.max' => 'Subdomain must not exceed 100 characters.',
            'database_name.required' => 'Database name is required.',
            'database_name.regex' => 'Database name can only contain letters, numbers, and underscores.',
            'database_name.unique' => 'This database name is already in use.',
            'database_name.max' => 'Database name must not exceed 100 characters.',
            'database_connection.required' => 'Database connection is required.',
            'database_connection.max' => 'Database connection must not exceed 50 characters.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be active, inactive, suspended, or maintenance.',
            'plan.required' => 'Plan is required.',
            'plan.in' => 'Plan must be free, basic, premium, or enterprise.',
            'max_users.min' => 'Maximum users must be at least 1.',
            'max_users.max' => 'Maximum users cannot exceed 999999.',
            'max_storage.min' => 'Maximum storage must be at least 1MB.',
            'max_storage.max' => 'Maximum storage cannot exceed 999999MB.',
            'max_bandwidth.min' => 'Maximum bandwidth must be at least 1MB.',
            'max_bandwidth.max' => 'Maximum bandwidth cannot exceed 999999MB.',
            'features.max' => 'Maximum 50 features are allowed.',
            'features.*.max' => 'Each feature must not exceed 100 characters.',
            'settings.max' => 'Maximum 100 settings are allowed.',
            'settings.*.max' => 'Setting value must not exceed 255 characters.',
            'owner_id.required' => 'Owner is required.',
            'owner_id.exists' => 'Owner user does not exist.',
            'billing_email.email' => 'Please enter a valid billing email address.',
            'billing_email.max' => 'Billing email must not exceed 255 characters.',
            'billing_phone.regex' => 'Please enter a valid billing phone number.',
            'billing_phone.max' => 'Billing phone must not exceed 20 characters.',
            'billing_address.max' => 'Billing address must not exceed 500 characters.',
            'billing_city.max' => 'Billing city must not exceed 100 characters.',
            'billing_country.max' => 'Billing country must not exceed 100 characters.',
            'billing_postal_code.max' => 'Billing postal code must not exceed 20 characters.',
            'trial_ends_at.after' => 'Trial end date must be in the future.',
            'subscription_ends_at.after' => 'Subscription end date must be in the future.',
            'last_activity_at.before_or_equal' => 'Last activity date cannot be in the future.',
        ]);
    }

    /**
     * Additional validation rules.
     */
    protected function additionalValidation($validator): void
    {
        $validator->after(function ($validator): void {
            // Validate domain format
            if ($this->filled('domain')) {
                $domain = $this->domain;

                if (! filter_var($domain, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
                    $validator->errors()->add(
                        'domain',
                        'Please enter a valid domain name.'
                    );
                }

                if (mb_strlen($domain) < 3) {
                    $validator->errors()->add(
                        'domain',
                        'Domain must be at least 3 characters long.'
                    );
                }
            }

            // Validate subdomain format
            if ($this->filled('subdomain')) {
                $subdomain = $this->subdomain;

                if (mb_strlen($subdomain) < 2) {
                    $validator->errors()->add(
                        'subdomain',
                        'Subdomain must be at least 2 characters long.'
                    );
                }

                if (mb_strlen($subdomain) > 100) {
                    $validator->errors()->add(
                        'subdomain',
                        'Subdomain must not exceed 100 characters.'
                    );
                }

                // Check for reserved subdomains
                $reservedSubdomains = ['www', 'admin', 'api', 'app', 'mail', 'ftp', 'blog', 'shop', 'store'];
                if (in_array(mb_strtolower($subdomain), $reservedSubdomains)) {
                    $validator->errors()->add(
                        'subdomain',
                        'This subdomain is reserved and cannot be used.'
                    );
                }
            }

            // Validate database name format
            if ($this->filled('database_name')) {
                $databaseName = $this->database_name;

                if (mb_strlen($databaseName) < 3) {
                    $validator->errors()->add(
                        'database_name',
                        'Database name must be at least 3 characters long.'
                    );
                }

                if (mb_strlen($databaseName) > 100) {
                    $validator->errors()->add(
                        'database_name',
                        'Database name must not exceed 100 characters.'
                    );
                }

                // Check for reserved database names
                $reservedDatabases = ['mysql', 'information_schema', 'performance_schema', 'sys', 'test'];
                if (in_array(mb_strtolower($databaseName), $reservedDatabases)) {
                    $validator->errors()->add(
                        'database_name',
                        'This database name is reserved and cannot be used.'
                    );
                }
            }

            // Validate database connection
            if ($this->filled('database_connection')) {
                $connection = $this->database_connection;

                if (mb_strlen($connection) < 3) {
                    $validator->errors()->add(
                        'database_connection',
                        'Database connection must be at least 3 characters long.'
                    );
                }

                if (mb_strlen($connection) > 50) {
                    $validator->errors()->add(
                        'database_connection',
                        'Database connection must not exceed 50 characters.'
                    );
                }
            }

            // Validate plan limits
            if ($this->filled('plan')) {
                $plan = $this->plan;
                $maxUsers = $this->max_users ?? 0;
                $maxStorage = $this->max_storage ?? 0;
                $maxBandwidth = $this->max_bandwidth ?? 0;

                switch ($plan) {
                    case 'free':
                        if ($maxUsers > 5) {
                            $validator->errors()->add(
                                'max_users',
                                'Free plan allows maximum 5 users.'
                            );
                        }
                        if ($maxStorage > 100) {
                            $validator->errors()->add(
                                'max_storage',
                                'Free plan allows maximum 100MB storage.'
                            );
                        }
                        if ($maxBandwidth > 1000) {
                            $validator->errors()->add(
                                'max_bandwidth',
                                'Free plan allows maximum 1GB bandwidth.'
                            );
                        }

                        break;
                    case 'basic':
                        if ($maxUsers > 50) {
                            $validator->errors()->add(
                                'max_users',
                                'Basic plan allows maximum 50 users.'
                            );
                        }
                        if ($maxStorage > 1000) {
                            $validator->errors()->add(
                                'max_storage',
                                'Basic plan allows maximum 1GB storage.'
                            );
                        }
                        if ($maxBandwidth > 10000) {
                            $validator->errors()->add(
                                'max_bandwidth',
                                'Basic plan allows maximum 10GB bandwidth.'
                            );
                        }

                        break;
                    case 'premium':
                        if ($maxUsers > 500) {
                            $validator->errors()->add(
                                'max_users',
                                'Premium plan allows maximum 500 users.'
                            );
                        }
                        if ($maxStorage > 10000) {
                            $validator->errors()->add(
                                'max_storage',
                                'Premium plan allows maximum 10GB storage.'
                            );
                        }
                        if ($maxBandwidth > 100000) {
                            $validator->errors()->add(
                                'max_bandwidth',
                                'Premium plan allows maximum 100GB bandwidth.'
                            );
                        }

                        break;
                    case 'enterprise':
                        // Enterprise plan has no limits
                        break;
                }
            }

            // Validate features
            if ($this->filled('features')) {
                $features = $this->features;

                if (count($features) > 50) {
                    $validator->errors()->add(
                        'features',
                        'Maximum 50 features are allowed.'
                    );
                }

                $validFeatures = [
                    'user_management', 'product_management', 'order_management',
                    'inventory_management', 'analytics', 'reporting', 'api_access',
                    'custom_domain', 'ssl_certificate', 'backup', 'support',
                    'white_label', 'custom_branding', 'advanced_analytics',
                    'multi_language', 'multi_currency', 'payment_gateways',
                    'shipping_methods', 'tax_management', 'discount_management',
                    'email_marketing', 'sms_marketing', 'push_notifications',
                    'social_login', 'two_factor_auth', 'audit_logs',
                    'role_permissions', 'api_rate_limiting', 'webhooks',
                    'custom_fields', 'workflows', 'automation',
                ];

                foreach ($features as $index => $feature) {
                    if (! in_array($feature, $validFeatures)) {
                        $validator->errors()->add(
                            'features.'.$index,
                            'Invalid feature: '.$feature
                        );
                    }
                }
            }

            // Validate settings
            if ($this->filled('settings')) {
                $settings = $this->settings;

                if (count($settings) > 100) {
                    $validator->errors()->add(
                        'settings',
                        'Maximum 100 settings are allowed.'
                    );
                }

                foreach ($settings as $key => $value) {
                    if (mb_strlen($key) > 100) {
                        $validator->errors()->add(
                            'settings.'.$key,
                            'Setting key must not exceed 100 characters.'
                        );
                    }

                    if (mb_strlen($value) > 255) {
                        $validator->errors()->add(
                            'settings.'.$key,
                            'Setting value must not exceed 255 characters.'
                        );
                    }
                }
            }

            // Validate owner
            if ($this->filled('owner_id')) {
                $ownerId = $this->owner_id;
                $owner = \Modules\User\Models\User::find($ownerId);

                if (! $owner) {
                    $validator->errors()->add(
                        'owner_id',
                        'Owner user does not exist.'
                    );
                } elseif (! $owner->is_active) {
                    $validator->errors()->add(
                        'owner_id',
                        'Owner user is not active.'
                    );
                }
            }

            // Validate billing email
            if ($this->filled('billing_email')) {
                $billingEmail = $this->billing_email;

                if (! filter_var($billingEmail, FILTER_VALIDATE_EMAIL)) {
                    $validator->errors()->add(
                        'billing_email',
                        'Please enter a valid billing email address.'
                    );
                }
            }

            // Validate billing phone
            if ($this->filled('billing_phone')) {
                $billingPhone = preg_replace('/[^0-9+]/', '', $this->billing_phone);

                if (mb_strlen($billingPhone) < 10 || mb_strlen($billingPhone) > 15) {
                    $validator->errors()->add(
                        'billing_phone',
                        'Billing phone must be between 10 and 15 digits.'
                    );
                }
            }

            // Validate billing address
            if ($this->filled('billing_address') && mb_strlen($this->billing_address) > 500) {
                $validator->errors()->add(
                    'billing_address',
                    'Billing address must not exceed 500 characters.'
                );
            }

            // Validate billing city
            if ($this->filled('billing_city') && mb_strlen($this->billing_city) > 100) {
                $validator->errors()->add(
                    'billing_city',
                    'Billing city must not exceed 100 characters.'
                );
            }

            // Validate billing country
            if ($this->filled('billing_country') && mb_strlen($this->billing_country) > 100) {
                $validator->errors()->add(
                    'billing_country',
                    'Billing country must not exceed 100 characters.'
                );
            }

            // Validate billing postal code
            if ($this->filled('billing_postal_code') && mb_strlen($this->billing_postal_code) > 20) {
                $validator->errors()->add(
                    'billing_postal_code',
                    'Billing postal code must not exceed 20 characters.'
                );
            }

            // Validate trial ends at
            if ($this->filled('trial_ends_at')) {
                $trialEndsAt = $this->trial_ends_at;

                if ($trialEndsAt <= now()) {
                    $validator->errors()->add(
                        'trial_ends_at',
                        'Trial end date must be in the future.'
                    );
                }
            }

            // Validate subscription ends at
            if ($this->filled('subscription_ends_at')) {
                $subscriptionEndsAt = $this->subscription_ends_at;

                if ($subscriptionEndsAt <= now()) {
                    $validator->errors()->add(
                        'subscription_ends_at',
                        'Subscription end date must be in the future.'
                    );
                }
            }

            // Validate last activity at
            if ($this->filled('last_activity_at')) {
                $lastActivityAt = $this->last_activity_at;

                if ($lastActivityAt > now()) {
                    $validator->errors()->add(
                        'last_activity_at',
                        'Last activity date cannot be in the future.'
                    );
                }
            }
        });
    }
}
