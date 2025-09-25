<?php

declare(strict_types=1);

namespace Modules\Complaint\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;

class ComplaintRequest extends BaseRequest
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
            'order_id' => [
                'nullable',
                'integer',
                'exists:orders,id',
            ],
            'product_id' => [
                'nullable',
                'integer',
                'exists:products,id',
            ],
            'type' => [
                'required',
                'string',
                'in:product_quality,delivery_issue,payment_problem,service_quality,refund_request,other',
            ],
            'priority' => [
                'required',
                'string',
                'in:low,medium,high,urgent',
            ],
            'status' => [
                'required',
                'string',
                'in:open,in_progress,resolved,closed,rejected',
            ],
            'subject' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'required',
                'string',
                'max:5000',
            ],
            'resolution' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'resolution_date' => [
                'nullable',
                'date',
                'after_or_equal:created_at',
            ],
            'assigned_to' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],
            'assigned_at' => [
                'nullable',
                'date',
                'before_or_equal:now',
            ],
            'due_date' => [
                'nullable',
                'date',
                'after:created_at',
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
            'notify_user' => [
                'boolean',
            ],
            'notify_admin' => [
                'boolean',
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
            'user_id.required' => 'User is required.',
            'user_id.exists' => 'User does not exist.',
            'order_id.exists' => 'Order does not exist.',
            'product_id.exists' => 'Product does not exist.',
            'type.required' => 'Complaint type is required.',
            'type.in' => 'Invalid complaint type.',
            'priority.required' => 'Priority is required.',
            'priority.in' => 'Invalid priority level.',
            'status.required' => 'Status is required.',
            'status.in' => 'Invalid complaint status.',
            'subject.required' => 'Subject is required.',
            'subject.min' => 'Subject must be at least 5 characters long.',
            'subject.max' => 'Subject must not exceed 255 characters.',
            'description.required' => 'Description is required.',
            'description.min' => 'Description must be at least 20 characters long.',
            'description.max' => 'Description must not exceed 5000 characters.',
            'resolution.max' => 'Resolution must not exceed 2000 characters.',
            'resolution_date.after_or_equal' => 'Resolution date cannot be before complaint creation date.',
            'assigned_to.exists' => 'Assigned user does not exist.',
            'assigned_at.before_or_equal' => 'Assigned date cannot be in the future.',
            'due_date.after' => 'Due date must be after complaint creation date.',
            'tags.max' => 'Maximum 10 tags are allowed.',
            'tags.*.max' => 'Each tag must not exceed 50 characters.',
            'attachments.max' => 'Maximum 5 attachments are allowed.',
            'attachments.*.max' => 'Each attachment must not exceed 10MB.',
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

            // Validate order
            if ($this->filled('order_id')) {
                $orderId = $this->order_id;
                $order = \Modules\Order\Models\Order::find($orderId);

                if (! $order) {
                    $validator->errors()->add(
                        'order_id',
                        'Order does not exist.'
                    );
                } elseif ($order->user_id !== $this->user_id) {
                    $validator->errors()->add(
                        'order_id',
                        'Order does not belong to the specified user.'
                    );
                }
            }

            // Validate product
            if ($this->filled('product_id')) {
                $productId = $this->product_id;
                $product = \Modules\Product\Models\Product::find($productId);

                if (! $product) {
                    $validator->errors()->add(
                        'product_id',
                        'Product does not exist.'
                    );
                } elseif (! $product->is_active) {
                    $validator->errors()->add(
                        'product_id',
                        'Product is not active.'
                    );
                }
            }

            // Validate complaint type
            if ($this->filled('type')) {
                $type = $this->type;
                $validTypes = [
                    'product_quality', 'delivery_issue', 'payment_problem',
                    'service_quality', 'refund_request', 'other',
                ];

                if (! in_array($type, $validTypes)) {
                    $validator->errors()->add(
                        'type',
                        'Invalid complaint type.'
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
                $validStatuses = ['open', 'in_progress', 'resolved', 'closed', 'rejected'];

                if (! in_array($status, $validStatuses)) {
                    $validator->errors()->add(
                        'status',
                        'Invalid complaint status.'
                    );
                }
            }

            // Validate subject
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

            // Validate description
            if ($this->filled('description')) {
                $description = $this->description;

                if (mb_strlen($description) < 20) {
                    $validator->errors()->add(
                        'description',
                        'Description must be at least 20 characters long.'
                    );
                }

                if (mb_strlen($description) > 5000) {
                    $validator->errors()->add(
                        'description',
                        'Description must not exceed 5000 characters.'
                    );
                }

                // Check for spam-like content
                $spamKeywords = ['viagra', 'casino', 'lottery', 'winner', 'free money'];
                $descriptionLower = mb_strtolower($description);

                foreach ($spamKeywords as $keyword) {
                    if (mb_strpos($descriptionLower, $keyword) !== false) {
                        $validator->errors()->add(
                            'description',
                            'Description contains potentially spam content.'
                        );

                        break;
                    }
                }
            }

            // Validate resolution
            if ($this->filled('resolution') && mb_strlen($this->resolution) > 2000) {
                $validator->errors()->add(
                    'resolution',
                    'Resolution must not exceed 2000 characters.'
                );
            }

            // Validate resolution date
            if ($this->filled('resolution_date')) {
                $resolutionDate = $this->resolution_date;
                $createdAt = $this->created_at ?? now();

                if ($resolutionDate < $createdAt) {
                    $validator->errors()->add(
                        'resolution_date',
                        'Resolution date cannot be before complaint creation date.'
                    );
                }
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

            // Validate assigned date
            if ($this->filled('assigned_at') && $this->assigned_at > now()) {
                $validator->errors()->add(
                    'assigned_at',
                    'Assigned date cannot be in the future.'
                );
            }

            // Validate due date
            if ($this->filled('due_date')) {
                $dueDate = $this->due_date;
                $createdAt = $this->created_at ?? now();

                if ($dueDate <= $createdAt) {
                    $validator->errors()->add(
                        'due_date',
                        'Due date must be after complaint creation date.'
                    );
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

            // Validate complaint type and product relationship
            if ($this->filled('type') && $this->filled('product_id')) {
                $type = $this->type;
                $productId = $this->product_id;

                if ($type === 'product_quality' && ! $productId) {
                    $validator->errors()->add(
                        'product_id',
                        'Product ID is required for product quality complaints.'
                    );
                }
            }

            // Validate complaint type and order relationship
            if ($this->filled('type') && $this->filled('order_id')) {
                $type = $this->type;
                $orderId = $this->order_id;

                if (in_array($type, ['delivery_issue', 'payment_problem', 'refund_request']) && ! $orderId) {
                    $validator->errors()->add(
                        'order_id',
                        'Order ID is required for '.str_replace('_', ' ', $type).' complaints.'
                    );
                }
            }
        });
    }
}
