<?php

declare(strict_types=1);

namespace Modules\Coupon\DTOs;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

readonly class CouponDTO
{
    public function __construct(
        public ?int $id,
        public ?string $code,
        public ?string $type = null,
        public ?float $value = null,
        public ?string $status = null,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
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
            $data['type'] ?? null,
            isset($data['value']) ? (float) $data['value'] : null,
            $data['status'] ?? null,
            isset($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            isset($data['updated_at']) ? Carbon::parse($data['updated_at']) : null,
        );
    }
}
