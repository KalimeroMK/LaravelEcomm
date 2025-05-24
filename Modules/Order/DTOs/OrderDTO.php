<?php

declare(strict_types=1);

namespace Modules\Order\DTOs;

use Illuminate\Http\Request;
use LaravelIdea\Helper\Modules\Core\Models\_IH_Core_C;
use LaravelIdea\Helper\Modules\Order\Models\_IH_Order_C;
use Modules\Core\Models\Core;
use Modules\Order\Models\Order;

readonly class OrderDTO
{
    public function __construct(
        public _IH_Core_C|array|Order|Core|_IH_Order_C|null $id,
        public ?int $user_id,
        public ?float $total,
        public ?string $created_at = null
    ) {
    }

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

}
