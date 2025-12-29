<?php

declare(strict_types=1);

namespace Modules\Billing\DTOs;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Modules\Billing\Models\Payment;

readonly class PaymentDTO
{
    public function __construct(
        public ?int $id,
        public ?int $order_id,
        public ?int $invoice_id,
        public ?int $user_id,
        public ?string $payment_method,
        public ?string $status,
        public ?float $amount,
        public ?string $currency,
        public ?string $transaction_id,
        public ?string $transaction_reference,
        public ?string $notes,
        public ?array $metadata,
        public ?Carbon $processed_at,
    ) {}

    public static function fromRequest(Request $request, ?int $id = null, ?Payment $existing = null): self
    {
        $validated = $request->validated();

        return new self(
            $id,
            isset($validated['order_id']) ? (int) $validated['order_id'] : $existing?->order_id,
            isset($validated['invoice_id']) ? (int) $validated['invoice_id'] : $existing?->invoice_id,
            isset($validated['user_id']) ? (int) $validated['user_id'] : $existing?->user_id,
            $validated['payment_method'] ?? $existing?->payment_method ?? 'cod',
            $validated['status'] ?? $existing?->status ?? 'pending',
            isset($validated['amount']) ? (float) $validated['amount'] : $existing?->amount,
            $validated['currency'] ?? $existing?->currency ?? 'USD',
            $validated['transaction_id'] ?? $existing?->transaction_id,
            $validated['transaction_reference'] ?? $existing?->transaction_reference,
            $validated['notes'] ?? $existing?->notes,
            isset($validated['metadata']) ? (array) $validated['metadata'] : $existing?->metadata,
            isset($validated['processed_at']) ? Carbon::parse($validated['processed_at']) : $existing?->processed_at,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'order_id' => $this->order_id,
            'invoice_id' => $this->invoice_id,
            'user_id' => $this->user_id,
            'payment_method' => $this->payment_method,
            'status' => $this->status,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'transaction_id' => $this->transaction_id,
            'transaction_reference' => $this->transaction_reference,
            'notes' => $this->notes,
            'metadata' => $this->metadata,
            'processed_at' => $this->processed_at?->format('Y-m-d H:i:s'),
        ], fn ($value) => $value !== null);
    }
}
