<?php

declare(strict_types=1);

namespace Modules\Coupon\DTOs;

use Illuminate\Http\Request;

readonly class CouponDTO
{
    public function __construct(
        public ?int $id,
        public ?string $code,
        public ?float $discount = null,
        public ?string $description = null,
        public ?string $type = null,
        public ?string $expires_at = null
    ) {}

    public static function fromRequest(Request $request, ?int $id = null): self
    {
        return self::fromArray($request->validated() + ['id' => $id]);
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['code'] ?? null,
            $data['discount'] ?? null,
            $data['description'] ?? null,
            $data['type'] ?? null,
            $data['expires_at'] ?? null
        );
    }
}
