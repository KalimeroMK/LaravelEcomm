<?php

declare(strict_types=1);

namespace Modules\Order\DTOs;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Order\Models\Order;

readonly class OrderDTO
{
    public function __construct(
        public ?int $id,
        public ?string $order_number,
        public ?int $user_id,
        public ?float $sub_total,
        public ?int $shipping_id,
        public ?float $total_amount,
        public ?int $quantity,
        public ?string $payment_method,
        public ?string $payment_status,
        public ?string $status,
        public ?int $payer_id,
        public ?string $transaction_reference,
        public ?string $created_at = null,
    ) {}

    public static function fromRequest(Request $request, ?int $id = null, ?Order $existing = null): self
    {
        $validated = $request->validated();

        return new self(
            id: $id,
            order_number: $validated['order_number'] ?? $existing?->order_number,
            user_id: $validated['user_id'] ?? $existing?->user_id,
            sub_total: $validated['sub_total'] ?? $existing?->sub_total,
            shipping_id: $validated['shipping_id'] ?? $existing?->shipping_id,
            total_amount: $validated['total_amount'] ?? $existing?->total_amount,
            quantity: $validated['quantity'] ?? $existing?->quantity,
            payment_method: $validated['payment_method'] ?? $existing?->payment_method,
            payment_status: $validated['payment_status'] ?? $existing?->payment_status,
            status: $validated['status'] ?? $existing?->status,
            payer_id: isset($validated['payer_id']) ? (int) $validated['payer_id'] : $existing?->payer_id,
            transaction_reference: $validated['transaction_reference'] ?? $existing?->transaction_reference,
            created_at: $validated['created_at'] ?? ($existing?->created_at?->format('Y-m-d H:i:s')),
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['order_number'] ?? null,
            $data['user_id'] ?? null,
            $data['sub_total'] ?? null,
            $data['shipping_id'] ?? null,
            $data['total_amount'] ?? null,
            $data['quantity'] ?? null,
            $data['payment_method'] ?? null,
            $data['payment_status'] ?? null,
            $data['status'] ?? null,
            isset($data['payer_id']) ? (int) $data['payer_id'] : null,
            $data['transaction_reference'] ?? null,
            isset($data['created_at']) && $data['created_at'] instanceof Carbon
                ? $data['created_at']->format('Y-m-d H:i:s')
                : $data['created_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'order_number' => $this->order_number,
            'user_id' => $this->user_id,
            'sub_total' => $this->sub_total,
            'shipping_id' => $this->shipping_id,
            'total_amount' => $this->total_amount,
            'quantity' => $this->quantity,
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'status' => $this->status,
            'payer_id' => $this->payer_id,
            'transaction_reference' => $this->transaction_reference,
        ], fn (float|int|string|null $v): bool => $v !== null);
    }
}
