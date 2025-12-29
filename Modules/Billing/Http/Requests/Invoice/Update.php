<?php

declare(strict_types=1);

namespace Modules\Billing\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $invoiceId = $this->route('invoice')->id ?? null;

        return [
            'order_id' => 'nullable|exists:orders,id',
            'user_id' => 'sometimes|exists:users,id',
            'invoice_number' => 'sometimes|string|max:50|unique:invoices,invoice_number,'.$invoiceId,
            'status' => 'sometimes|in:draft,sent,viewed,paid,overdue,cancelled,refunded',
            'issue_date' => 'sometimes|date',
            'due_date' => 'sometimes|date|after_or_equal:issue_date',
            'paid_date' => 'nullable|date|after_or_equal:issue_date',
            'subtotal' => 'sometimes|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'sometimes|numeric|min:0',
            'notes' => 'nullable|string',
        ];
    }
}
