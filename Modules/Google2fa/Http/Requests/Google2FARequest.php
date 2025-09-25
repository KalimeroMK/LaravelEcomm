<?php

declare(strict_types=1);

namespace Modules\Google2fa\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;

class Google2FARequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return array_merge([
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],
            'secret_key' => [
                'required',
                'string',
                'size:32',
                'regex:/^[A-Z2-7]+$/',
            ],
            'qr_code' => [
                'nullable',
                'string',
                'max:500',
            ],
            'backup_codes' => [
                'nullable',
                'array',
                'min:8',
                'max:10',
            ],
            'backup_codes.*' => [
                'string',
                'size:8',
                'regex:/^[0-9]+$/',
            ],
            'is_enabled' => [
                'boolean',
            ],
            'last_used_at' => [
                'nullable',
                'date',
                'before_or_equal:now',
            ],
            'expires_at' => [
                'nullable',
                'date',
                'after:now',
            ],
            'attempts' => [
                'nullable',
                'integer',
                'min:0',
                'max:5',
            ],
            'max_attempts' => [
                'nullable',
                'integer',
                'min:1',
                'max:10',
            ],
            'locked_until' => [
                'nullable',
                'date',
                'after:now',
            ],
            'ip_address' => [
                'nullable',
                'ip',
                'max:45',
            ],
            'user_agent' => [
                'nullable',
                'string',
                'max:500',
            ],
            'device_name' => [
                'nullable',
                'string',
                'max:100',
            ],
            'device_type' => [
                'nullable',
                'string',
                'in:mobile,tablet,desktop,other',
            ],
            'browser' => [
                'nullable',
                'string',
                'max:100',
            ],
            'os' => [
                'nullable',
                'string',
                'max:100',
            ],
            'country' => [
                'nullable',
                'string',
                'size:2',
                'regex:/^[A-Z]{2}$/',
            ],
            'city' => [
                'nullable',
                'string',
                'max:100',
            ],
            'timezone' => [
                'nullable',
                'string',
                'max:50',
            ],
            'language' => [
                'nullable',
                'string',
                'size:2',
                'regex:/^[a-z]{2}$/',
            ],
            'is_trusted' => [
                'boolean',
            ],
            'is_remembered' => [
                'boolean',
            ],
            'remember_until' => [
                'nullable',
                'date',
                'after:now',
            ],
            'is_forced' => [
                'boolean',
            ],
            'is_optional' => [
                'boolean',
            ],
            'is_required' => [
                'boolean',
            ],
            'is_disabled' => [
                'boolean',
            ],
            'is_locked' => [
                'boolean',
            ],
            'is_suspended' => [
                'boolean',
            ],
            'is_banned' => [
                'boolean',
            ],
            'is_blacklisted' => [
                'boolean',
            ],
            'is_whitelisted' => [
                'boolean',
            ],
            'is_verified' => [
                'boolean',
            ],
            'is_approved' => [
                'boolean',
            ],
            'is_rejected' => [
                'boolean',
            ],
            'is_pending' => [
                'boolean',
            ],
            'is_processing' => [
                'boolean',
            ],
            'is_completed' => [
                'boolean',
            ],
            'is_failed' => [
                'boolean',
            ],
            'is_cancelled' => [
                'boolean',
            ],
            'is_expired' => [
                'boolean',
            ],
            'is_revoked' => [
                'boolean',
            ],
            'is_invalid' => [
                'boolean',
            ],
            'is_valid' => [
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
            'user_id.required' => 'User is required.',
            'user_id.exists' => 'User does not exist.',
            'secret_key.required' => 'Secret key is required.',
            'secret_key.size' => 'Secret key must be exactly 32 characters long.',
            'secret_key.regex' => 'Secret key must contain only uppercase letters A-Z and numbers 2-7.',
            'qr_code.max' => 'QR code must not exceed 500 characters.',
            'backup_codes.min' => 'At least 8 backup codes are required.',
            'backup_codes.max' => 'Maximum 10 backup codes are allowed.',
            'backup_codes.*.size' => 'Each backup code must be exactly 8 characters long.',
            'backup_codes.*.regex' => 'Each backup code must contain only numbers.',
            'last_used_at.before_or_equal' => 'Last used date cannot be in the future.',
            'expires_at.after' => 'Expires date must be in the future.',
            'attempts.min' => 'Attempts cannot be negative.',
            'attempts.max' => 'Attempts cannot exceed 5.',
            'max_attempts.min' => 'Max attempts must be at least 1.',
            'max_attempts.max' => 'Max attempts cannot exceed 10.',
            'locked_until.after' => 'Locked until date must be in the future.',
            'ip_address.ip' => 'Please enter a valid IP address.',
            'ip_address.max' => 'IP address must not exceed 45 characters.',
            'user_agent.max' => 'User agent must not exceed 500 characters.',
            'device_name.max' => 'Device name must not exceed 100 characters.',
            'device_type.in' => 'Device type must be mobile, tablet, desktop, or other.',
            'browser.max' => 'Browser must not exceed 100 characters.',
            'os.max' => 'OS must not exceed 100 characters.',
            'country.size' => 'Country must be exactly 2 characters long.',
            'country.regex' => 'Country must be in uppercase format (e.g., US, CA, GB).',
            'city.max' => 'City must not exceed 100 characters.',
            'timezone.max' => 'Timezone must not exceed 50 characters.',
            'language.size' => 'Language must be exactly 2 characters long.',
            'language.regex' => 'Language must be in lowercase format (e.g., en, es, fr).',
            'remember_until.after' => 'Remember until date must be in the future.',
        ]);
    }

    /**
     * Additional validation rules.
     */
    protected function additionalValidation($validator): void
    {
        $validator->after(function ($validator): void {
            // Validate user
            if ($this->filled('user_id')) {
                $userId = $this->user_id;
                $user = \Modules\User\Models\User::find($userId);

                if (! $user) {
                    $validator->errors()->add(
                        'user_id',
                        'User does not exist.'
                    );
                } elseif (! $user->is_active) {
                    $validator->errors()->add(
                        'user_id',
                        'User is not active.'
                    );
                }
            }

            // Validate secret key format
            if ($this->filled('secret_key')) {
                $secretKey = $this->secret_key;

                if (mb_strlen($secretKey) !== 32) {
                    $validator->errors()->add(
                        'secret_key',
                        'Secret key must be exactly 32 characters long.'
                    );
                }

                if (! preg_match('/^[A-Z2-7]+$/', $secretKey)) {
                    $validator->errors()->add(
                        'secret_key',
                        'Secret key must contain only uppercase letters A-Z and numbers 2-7.'
                    );
                }
            }

            // Validate QR code
            if ($this->filled('qr_code') && mb_strlen($this->qr_code) > 500) {
                $validator->errors()->add(
                    'qr_code',
                    'QR code must not exceed 500 characters.'
                );
            }

            // Validate backup codes
            if ($this->filled('backup_codes')) {
                $backupCodes = $this->backup_codes;

                if (count($backupCodes) < 8) {
                    $validator->errors()->add(
                        'backup_codes',
                        'At least 8 backup codes are required.'
                    );
                }

                if (count($backupCodes) > 10) {
                    $validator->errors()->add(
                        'backup_codes',
                        'Maximum 10 backup codes are allowed.'
                    );
                }

                foreach ($backupCodes as $index => $code) {
                    if (mb_strlen($code) !== 8) {
                        $validator->errors()->add(
                            'backup_codes.'.$index,
                            'Each backup code must be exactly 8 characters long.'
                        );
                    }

                    if (! preg_match('/^d+$/', $code)) {
                        $validator->errors()->add(
                            'backup_codes.'.$index,
                            'Each backup code must contain only numbers.'
                        );
                    }
                }
            }

            // Validate last used date
            if ($this->filled('last_used_at') && $this->last_used_at > now()) {
                $validator->errors()->add(
                    'last_used_at',
                    'Last used date cannot be in the future.'
                );
            }

            // Validate expires date
            if ($this->filled('expires_at') && $this->expires_at <= now()) {
                $validator->errors()->add(
                    'expires_at',
                    'Expires date must be in the future.'
                );
            }

            // Validate attempts
            if ($this->filled('attempts')) {
                $attempts = $this->attempts;

                if ($attempts < 0) {
                    $validator->errors()->add(
                        'attempts',
                        'Attempts cannot be negative.'
                    );
                }

                if ($attempts > 5) {
                    $validator->errors()->add(
                        'attempts',
                        'Attempts cannot exceed 5.'
                    );
                }
            }

            // Validate max attempts
            if ($this->filled('max_attempts')) {
                $maxAttempts = $this->max_attempts;

                if ($maxAttempts < 1) {
                    $validator->errors()->add(
                        'max_attempts',
                        'Max attempts must be at least 1.'
                    );
                }

                if ($maxAttempts > 10) {
                    $validator->errors()->add(
                        'max_attempts',
                        'Max attempts cannot exceed 10.'
                    );
                }
            }

            // Validate locked until date
            if ($this->filled('locked_until') && $this->locked_until <= now()) {
                $validator->errors()->add(
                    'locked_until',
                    'Locked until date must be in the future.'
                );
            }

            // Validate IP address
            if ($this->filled('ip_address')) {
                $ipAddress = $this->ip_address;

                if (! filter_var($ipAddress, FILTER_VALIDATE_IP)) {
                    $validator->errors()->add(
                        'ip_address',
                        'Please enter a valid IP address.'
                    );
                }
            }

            // Validate user agent
            if ($this->filled('user_agent') && mb_strlen($this->user_agent) > 500) {
                $validator->errors()->add(
                    'user_agent',
                    'User agent must not exceed 500 characters.'
                );
            }

            // Validate device name
            if ($this->filled('device_name') && mb_strlen($this->device_name) > 100) {
                $validator->errors()->add(
                    'device_name',
                    'Device name must not exceed 100 characters.'
                );
            }

            // Validate device type
            if ($this->filled('device_type')) {
                $deviceType = $this->device_type;
                $validTypes = ['mobile', 'tablet', 'desktop', 'other'];

                if (! in_array($deviceType, $validTypes)) {
                    $validator->errors()->add(
                        'device_type',
                        'Invalid device type.'
                    );
                }
            }

            // Validate browser
            if ($this->filled('browser') && mb_strlen($this->browser) > 100) {
                $validator->errors()->add(
                    'browser',
                    'Browser must not exceed 100 characters.'
                );
            }

            // Validate OS
            if ($this->filled('os') && mb_strlen($this->os) > 100) {
                $validator->errors()->add(
                    'os',
                    'OS must not exceed 100 characters.'
                );
            }

            // Validate country
            if ($this->filled('country')) {
                $country = $this->country;

                if (mb_strlen($country) !== 2) {
                    $validator->errors()->add(
                        'country',
                        'Country must be exactly 2 characters long.'
                    );
                }

                if (! preg_match('/^[A-Z]{2}$/', $country)) {
                    $validator->errors()->add(
                        'country',
                        'Country must be in uppercase format (e.g., US, CA, GB).'
                    );
                }
            }

            // Validate city
            if ($this->filled('city') && mb_strlen($this->city) > 100) {
                $validator->errors()->add(
                    'city',
                    'City must not exceed 100 characters.'
                );
            }

            // Validate timezone
            if ($this->filled('timezone') && mb_strlen($this->timezone) > 50) {
                $validator->errors()->add(
                    'timezone',
                    'Timezone must not exceed 50 characters.'
                );
            }

            // Validate language
            if ($this->filled('language')) {
                $language = $this->language;

                if (mb_strlen($language) !== 2) {
                    $validator->errors()->add(
                        'language',
                        'Language must be exactly 2 characters long.'
                    );
                }

                if (! preg_match('/^[a-z]{2}$/', $language)) {
                    $validator->errors()->add(
                        'language',
                        'Language must be in lowercase format (e.g., en, es, fr).'
                    );
                }
            }

            // Validate remember until date
            if ($this->filled('remember_until') && $this->remember_until <= now()) {
                $validator->errors()->add(
                    'remember_until',
                    'Remember until date must be in the future.'
                );
            }
        });
    }
}
