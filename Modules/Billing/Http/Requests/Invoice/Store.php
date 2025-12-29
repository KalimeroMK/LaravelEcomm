<?php

declare(strict_types=1);

namespace Modules\Billing\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_id' => 'nullable|exists:orders,id',
            'user_id' => 'required|exists:users,id',
            'invoice_number' => 'nullable|string|max:50|unique:invoices,invoice_number',
            'status' => 'required|in:draft,sent,viewed,paid,overdue,cancelled,refunded',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'paid_date' => 'nullable|date|after_or_equal:issue_date',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ];
    }
}
