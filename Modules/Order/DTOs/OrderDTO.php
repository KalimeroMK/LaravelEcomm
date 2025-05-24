<?php

declare(strict_types=1);

namespace Modules\Order\DTOs;

use Illuminate\Http\Request;
use Modules\Order\Models\Order;

readonly class OrderDTO
{
    public function __construct(
        public ?int $id,
        public ?int $user_id,
        public ?float $total,
        public ?string $created_at = null
    ) {}

    public static function fromRequest(Request $request, ?int $id = null): self
    {
        return self::fromArray($request->validated() + ['id' => $id]);
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['user_id'] ?? null,
            $data['total'] ?? null,
            $data['created_at'] ?? null
        );
    }

    public static function fromOrder(Order $order): self
    {
        return new self(
            $order->id,
            $order->user_id,
            $order->total,
            $order->created_at->toDateTimeString()
        );
    }
}
