<?php

declare(strict_types=1);

namespace Modules\Order\DTOs;

use Illuminate\Http\Request;

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
        public ?string $payer_id,
        public ?string $transaction_reference,
        public ?string $post_code,
        public ?string $created_at = null,
    ) {}

    public static function fromRequest(Request $request, ?int $id = null): self
    {
        return self::fromArray($request->validated() + ['id' => $id]);
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
            $data['payer_id'] ?? null,
            $data['transaction_reference'] ?? null,
            $data['post_code'] ?? null,
            $data['created_at'] ?? null,
        );
    }

    public function withId(int $id): self
    {
        return new self(
            $id,
            $this->order_number,
            $this->user_id,
            $this->sub_total,
            $this->shipping_id,
            $this->total_amount,
            $this->quantity,
            $this->payment_method,
            $this->payment_status,
            $this->status,
            $this->payer_id,
            $this->transaction_reference,
            $this->post_code,
            $this->created_at,
        );
    }
}
