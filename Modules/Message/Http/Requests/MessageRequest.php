<?php

declare(strict_types=1);

namespace Modules\Message\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;

class MessageRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
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
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[\+]?[1-9][\d]{0,15}$/',
            ],
            'subject' => [
                'required',
                'string',
                'max:255',
            ],
            'message' => [
                'required',
                'string',
                'max:5000',
            ],
            'type' => [
                'required',
                'string',
                'in:contact,support,complaint,suggestion,feedback,general',
            ],
            'priority' => [
                'nullable',
                'string',
                'in:low,medium,high,urgent',
            ],
            'status' => [
                'nullable',
                'string',
                'in:new,read,replied,closed,archived',
            ],
            'department' => [
                'nullable',
                'string',
                'max:100',
            ],
            'assigned_to' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],
            'attachments' => [
                'nullable',
                'array',
                'max:5',
            ],
            'attachments.*' => [
                'file',
                'mimes:pdf,doc,docx,txt,jpg,jpeg,png,gif,webp',
                'max:10240',
            ],
            'is_public' => [
                'boolean',
            ],
            'allow_reply' => [
                'boolean',
            ],
            'tags' => [
                'nullable',
                'array',
                'max:10',
            ],
            'tags.*' => [
                'string',
                'max:50',
            ],
            'custom_fields' => [
                'nullable',
                'array',
            ],
            'custom_fields.*' => [
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
            'name.required' => 'Name is required.',
            'name.regex' => 'Name can only contain letters and spaces.',
            'name.min' => 'Name must be at least 2 characters long.',
            'name.max' => 'Name must not exceed 255 characters.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'Email address must not exceed 255 characters.',
            'phone.regex' => 'Please enter a valid phone number.',
            'phone.max' => 'Phone number must not exceed 20 characters.',
            'subject.required' => 'Subject is required.',
            'subject.min' => 'Subject must be at least 5 characters long.',
            'subject.max' => 'Subject must not exceed 255 characters.',
            'message.required' => 'Message content is required.',
            'message.min' => 'Message must be at least 10 characters long.',
            'message.max' => 'Message must not exceed 5000 characters.',
            'type.required' => 'Message type is required.',
            'type.in' => 'Invalid message type.',
            'priority.in' => 'Invalid priority level.',
            'status.in' => 'Invalid message status.',
            'department.max' => 'Department must not exceed 100 characters.',
            'assigned_to.exists' => 'Assigned user does not exist.',
            'attachments.max' => 'Maximum 5 attachments are allowed.',
            'attachments.*.max' => 'Each attachment must not exceed 10MB.',
            'tags.max' => 'Maximum 10 tags are allowed.',
            'tags.*.max' => 'Each tag must not exceed 50 characters.',
            'custom_fields.max' => 'Maximum 20 custom fields are allowed.',
            'custom_fields.*.max' => 'Custom field value must not exceed 255 characters.',
        ]);
    }

    /**
     * Additional validation rules.
     */
    protected function additionalValidation($validator): void
    {
        $validator->after(function ($validator): void {
            // Validate name format
            if ($this->filled('name')) {
                $name = $this->name;

                if (mb_strlen($name) < 2) {
                    $validator->errors()->add(
                        'name',
                        'Name must be at least 2 characters long.'
                    );
                }

                if (mb_strlen($name) > 255) {
                    $validator->errors()->add(
                        'name',
                        'Name must not exceed 255 characters.'
                    );
                }
            }

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

            // Validate subject length
            if ($this->filled('subject')) {
                $subject = $this->subject;

                if (mb_strlen($subject) < 5) {
                    $validator->errors()->add(
                        'subject',
                        'Subject must be at least 5 characters long.'
                    );
                }

                if (mb_strlen($subject) > 255) {
                    $validator->errors()->add(
                        'subject',
                        'Subject must not exceed 255 characters.'
                    );
                }
            }

            // Validate message content
            if ($this->filled('message')) {
                $message = $this->message;

                if (mb_strlen($message) < 10) {
                    $validator->errors()->add(
                        'message',
                        'Message must be at least 10 characters long.'
                    );
                }

                if (mb_strlen($message) > 5000) {
                    $validator->errors()->add(
                        'message',
                        'Message must not exceed 5000 characters.'
                    );
                }

                // Check for spam-like content
                $spamKeywords = ['viagra', 'casino', 'lottery', 'winner', 'free money'];
                $messageLower = mb_strtolower($message);

                foreach ($spamKeywords as $keyword) {
                    if (mb_strpos($messageLower, $keyword) !== false) {
                        $validator->errors()->add(
                            'message',
                            'Message contains potentially spam content.'
                        );

                        break;
                    }
                }
            }

            // Validate message type
            if ($this->filled('type')) {
                $type = $this->type;
                $validTypes = ['contact', 'support', 'complaint', 'suggestion', 'feedback', 'general'];

                if (! in_array($type, $validTypes)) {
                    $validator->errors()->add(
                        'type',
                        'Invalid message type.'
                    );
                }
            }

            // Validate priority
            if ($this->filled('priority')) {
                $priority = $this->priority;
                $validPriorities = ['low', 'medium', 'high', 'urgent'];

                if (! in_array($priority, $validPriorities)) {
                    $validator->errors()->add(
                        'priority',
                        'Invalid priority level.'
                    );
                }
            }

            // Validate status
            if ($this->filled('status')) {
                $status = $this->status;
                $validStatuses = ['new', 'read', 'replied', 'closed', 'archived'];

                if (! in_array($status, $validStatuses)) {
                    $validator->errors()->add(
                        'status',
                        'Invalid message status.'
                    );
                }
            }

            // Validate department
            if ($this->filled('department') && mb_strlen($this->department) > 100) {
                $validator->errors()->add(
                    'department',
                    'Department must not exceed 100 characters.'
                );
            }

            // Validate assigned user
            if ($this->filled('assigned_to')) {
                $assignedTo = $this->assigned_to;
                $user = \Modules\User\Models\User::find($assignedTo);

                if (! $user) {
                    $validator->errors()->add(
                        'assigned_to',
                        'Assigned user does not exist.'
                    );
                } elseif (! $user->is_active) {
                    $validator->errors()->add(
                        'assigned_to',
                        'Assigned user is not active.'
                    );
                }
            }

            // Validate attachments
            if ($this->filled('attachments')) {
                $attachments = $this->attachments;

                if (count($attachments) > 5) {
                    $validator->errors()->add(
                        'attachments',
                        'Maximum 5 attachments are allowed.'
                    );
                }

                foreach ($attachments as $index => $attachment) {
                    if ($attachment->getSize() > 10240) { // 10MB
                        $validator->errors()->add(
                            'attachments.'.$index,
                            'Each attachment must not exceed 10MB.'
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

                    if (mb_strlen($tag) > 50) {
                        $validator->errors()->add(
                            'tags.'.$index,
                            'Each tag must not exceed 50 characters.'
                        );
                    }
                }
            }

            // Validate custom fields
            if ($this->filled('custom_fields')) {
                $customFields = $this->custom_fields;

                if (count($customFields) > 20) {
                    $validator->errors()->add(
                        'custom_fields',
                        'Maximum 20 custom fields are allowed.'
                    );
                }

                foreach ($customFields as $key => $value) {
                    if (mb_strlen($key) > 50) {
                        $validator->errors()->add(
                            'custom_fields.'.$key,
                            'Custom field key must not exceed 50 characters.'
                        );
                    }

                    if (mb_strlen($value) > 255) {
                        $validator->errors()->add(
                            'custom_fields.'.$key,
                            'Custom field value must not exceed 255 characters.'
                        );
                    }
                }
            }
        });
    }
}
