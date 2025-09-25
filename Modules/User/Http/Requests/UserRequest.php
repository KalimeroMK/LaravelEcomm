<?php

declare(strict_types=1);

namespace Modules\User\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\BaseRequest;

class UserRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $userId = $this->route('user') ?? $this->route('id');

        return array_merge([
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z\s]+$/',
            ],
            'email' => [
                'required',
                'email:rfc,dns',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:255',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
            ],
            'password_confirmation' => [
                'required_with:password',
                'same:password',
            ],
            'status' => [
                'nullable',
                'string',
                Rule::in(['active', 'inactive', 'suspended']),
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[\+]?[1-9][\d]{0,15}$/',
            ],
            'address' => [
                'nullable',
                'string',
                'max:500',
            ],
            'city' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[a-zA-Z\s]+$/',
            ],
            'country' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[a-zA-Z\s]+$/',
            ],
            'postal_code' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[a-zA-Z0-9\s\-]+$/',
            ],
            'date_of_birth' => [
                'nullable',
                'date',
                'before:today',
                'after:1900-01-01',
            ],
            'gender' => [
                'nullable',
                'string',
                Rule::in(['male', 'female', 'other']),
            ],
            'newsletter_subscription' => [
                'boolean',
            ],
            'marketing_emails' => [
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
            'name.regex' => 'Name can only contain letters and spaces.',
            'email.email' => 'Please enter a valid email address.',
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
            'password_confirmation.same' => 'Password confirmation does not match.',
            'phone.regex' => 'Please enter a valid phone number.',
            'city.regex' => 'City can only contain letters and spaces.',
            'country.regex' => 'Country can only contain letters and spaces.',
            'postal_code.regex' => 'Postal code format is invalid.',
            'date_of_birth.before' => 'Date of birth must be in the past.',
            'date_of_birth.after' => 'Date of birth must be after 1900.',
        ]);
    }

    /**
     * Additional validation rules.
     */
    protected function additionalValidation($validator): void
    {
        $validator->after(function ($validator): void {
            // Validate password strength
            if ($this->filled('password')) {
                $password = $this->password;

                if (mb_strlen($password) < 8) {
                    $validator->errors()->add(
                        'password',
                        'Password must be at least 8 characters long.'
                    );
                }

                if (! preg_match('/[a-z]/', $password)) {
                    $validator->errors()->add(
                        'password',
                        'Password must contain at least one lowercase letter.'
                    );
                }

                if (! preg_match('/[A-Z]/', $password)) {
                    $validator->errors()->add(
                        'password',
                        'Password must contain at least one uppercase letter.'
                    );
                }

                if (! preg_match('/\d/', $password)) {
                    $validator->errors()->add(
                        'password',
                        'Password must contain at least one number.'
                    );
                }

                if (! preg_match('/[@$!%*?&]/', $password)) {
                    $validator->errors()->add(
                        'password',
                        'Password must contain at least one special character (@$!%*?&).'
                    );
                }
            }

            // Validate age
            if ($this->filled('date_of_birth')) {
                $age = now()->diffInYears($this->date_of_birth);
                if ($age < 13) {
                    $validator->errors()->add(
                        'date_of_birth',
                        'You must be at least 13 years old to register.'
                    );
                }
            }

            // Validate phone number format
            if ($this->filled('phone')) {
                $phone = preg_replace('/[^0-9+]/', '', $this->phone);
                if (mb_strlen($phone) < 10 || mb_strlen($phone) > 15) {
                    $validator->errors()->add(
                        'phone',
                        'Phone number must be between 10 and 15 digits.'
                    );
                }
            }
        });
    }
}
