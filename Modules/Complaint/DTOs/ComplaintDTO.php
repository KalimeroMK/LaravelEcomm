<?php

declare(strict_types=1);

namespace Modules\Complaint\DTOs;

use Illuminate\Http\Request;

readonly class ComplaintDTO
{
    public function __construct(
        public ?int $id,
        public int $user_id,
        public int $order_id,
        public string $description,
        public ?string $status = null
    ) {}

    public static function fromRequest(Request $request, ?int $id = null): self
    {
        return new self(
            $id,
            (int) $request->user()->id,
            (int) $request->input('order_id'),
            $request->input('description'),
            $request->input('status')
        );
    }
}
