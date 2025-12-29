<?php

declare(strict_types=1);

namespace Modules\Billing\DTOs;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Modules\Billing\Models\Invoice;

readonly class InvoiceDTO
{
    public function __construct(
        public ?int $id,
        public ?string $invoice_number,
        public ?int $order_id,
        public ?int $user_id,
        public ?string $status,
        public ?Carbon $issue_date,
        public ?Carbon $due_date,
        public ?Carbon $paid_date,
        public ?float $subtotal,
        public ?float $tax_amount,
        public ?float $discount_amount,
        public ?float $total_amount,
        public ?string $notes,
    ) {}

    public static function fromRequest(Request $request, ?int $id = null, ?Invoice $existing = null): self
    {
        $validated = $request->validated();

        return new self(
            $id,
            $validated['invoice_number'] ?? $existing?->invoice_number,
            isset($validated['order_id']) ? (int) $validated['order_id'] : $existing?->order_id,
            isset($validated['user_id']) ? (int) $validated['user_id'] : $existing?->user_id,
            $validated['status'] ?? $existing?->status ?? 'draft',
            isset($validated['issue_date']) ? Carbon::parse($validated['issue_date']) : $existing?->issue_date,
            isset($validated['due_date']) ? Carbon::parse($validated['due_date']) : $existing?->due_date,
            isset($validated['paid_date']) ? Carbon::parse($validated['paid_date']) : $existing?->paid_date,
            isset($validated['subtotal']) ? (float) $validated['subtotal'] : $existing?->subtotal,
            isset($validated['tax_amount']) ? (float) $validated['tax_amount'] : $existing?->tax_amount ?? 0,
            isset($validated['discount_amount']) ? (float) $validated['discount_amount'] : $existing?->discount_amount ?? 0,
            isset($validated['total_amount']) ? (float) $validated['total_amount'] : $existing?->total_amount,
            $validated['notes'] ?? $existing?->notes,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'invoice_number' => $this->invoice_number,
            'order_id' => $this->order_id,
            'user_id' => $this->user_id,
            'status' => $this->status,
            'issue_date' => $this->issue_date?->format('Y-m-d'),
            'due_date' => $this->due_date?->format('Y-m-d'),
            'paid_date' => $this->paid_date?->format('Y-m-d'),
            'subtotal' => $this->subtotal,
            'tax_amount' => $this->tax_amount,
            'discount_amount' => $this->discount_amount,
            'total_amount' => $this->total_amount,
            'notes' => $this->notes,
        ], fn ($value) => $value !== null);
    }
}
