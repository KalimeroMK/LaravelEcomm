<?php

declare(strict_types=1);

namespace Modules\Newsletter\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;

class NewsletterRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return array_merge([
            'email' => [
                'required',
                'email:rfc,dns',
                'max:255',
                'unique:newsletter_subscribers,email',
            ],
            'name' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-zA-Z\s]+$/',
            ],
            'status' => [
                'nullable',
                'string',
                'in:active,inactive,unsubscribed',
            ],
            'is_validated' => [
                'boolean',
            ],
            'subscription_date' => [
                'nullable',
                'date',
            ],
            'unsubscription_date' => [
                'nullable',
                'date',
                'after:subscription_date',
            ],
            'source' => [
                'nullable',
                'string',
                'max:100',
                'in:website,api,import,manual',
            ],
            'tags' => [
                'nullable',
                'array',
            ],
            'tags.*' => [
                'string',
                'max:50',
            ],
            'preferences' => [
                'nullable',
                'array',
            ],
            'preferences.newsletter' => [
                'boolean',
            ],
            'preferences.promotions' => [
                'boolean',
            ],
            'preferences.updates' => [
                'boolean',
            ],
            'preferences.events' => [
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
            'email.unique' => 'This email address is already subscribed to our newsletter.',
            'email.email' => 'Please enter a valid email address.',
            'name.regex' => 'Name can only contain letters and spaces.',
            'source.in' => 'Invalid subscription source.',
            'tags.max' => 'Maximum 10 tags are allowed.',
            'tags.*.max' => 'Each tag must not exceed 50 characters.',
            'preferences.*.boolean' => 'Preference values must be true or false.',
        ]);
    }

    /**
     * Additional validation rules.
     */
    protected function additionalValidation($validator): void
    {
        $validator->after(function ($validator): void {
            // Validate email domain
            if ($this->filled('email')) {
                $email = $this->email;
                $domain = mb_substr(mb_strrchr($email, '@'), 1);

                // Check for common disposable email domains
                $disposableDomains = [
                    '10minutemail.com', 'tempmail.org', 'guerrillamail.com',
                    'mailinator.com', 'yopmail.com', 'temp-mail.org',
                ];

                if (in_array(mb_strtolower($domain), $disposableDomains)) {
                    $validator->errors()->add(
                        'email',
                        'Please use a valid email address. Disposable email addresses are not allowed.'
                    );
                }
            }

            // Validate subscription dates
            if ($this->filled('subscription_date') && $this->filled('unsubscription_date') && $this->subscription_date >= $this->unsubscription_date) {
                $validator->errors()->add(
                    'unsubscription_date',
                    'Unsubscription date must be after subscription date.'
                );
            }

            // Validate preferences
            if ($this->filled('preferences')) {
                $preferences = $this->preferences;
                $validPreferences = ['newsletter', 'promotions', 'updates', 'events'];

                foreach ($preferences as $key => $value) {
                    if (! in_array($key, $validPreferences)) {
                        $validator->errors()->add(
                            'preferences.'.$key,
                            'Invalid preference type.'
                        );
                    }

                    if (! is_bool($value)) {
                        $validator->errors()->add(
                            'preferences.'.$key,
                            'Preference value must be true or false.'
                        );
                    }
                }
            }

            // Validate tags
            if ($this->filled('tags')) {
                $tags = $this->tags;
                if (count($tags) > 10) {
                    $validator->errors()->add(
                        'tags',
                        'Maximum 10 tags are allowed.'
                    );
                }

                foreach ($tags as $index => $tag) {
                    if (mb_strlen($tag) < 2) {
                        $validator->errors()->add(
                            'tags.'.$index,
                            'Each tag must be at least 2 characters long.'
                        );
                    }
                }
            }
        });
    }
}
