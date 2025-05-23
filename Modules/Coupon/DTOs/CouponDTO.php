<?php

declare(strict_types=1);

namespace Modules\Coupon\DTOs;

use Illuminate\Http\Request;

readonly class CouponDTO
{
    public function __construct(
        public ?int $id,
        public string $code,
        public float $discount,
        public ?string $description = null,
        public ?string $type = null,
        public ?string $expires_at = null
    ) {}

    public static function fromRequest(Request $request, ?int $id = null): self
    {
        return new self(
            $id,
            $request->input('code'),
            (float) $request->input('discount'),
            $request->input('description'),
            $request->input('type'),
            $request->input('expires_at')
        );
    }
}
