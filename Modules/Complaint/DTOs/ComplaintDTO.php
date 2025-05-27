<?php

declare(strict_types=1);

namespace Modules\Complaint\DTOs;

use Illuminate\Http\Request;

readonly class ComplaintDTO
{
    public function __construct(
        public ?int $id,
        public ?int $user_id,
        public ?int $order_id,
        public ?string $description,
        public ?string $status = null
    ) {}

    public static function fromRequest(Request $request, ?int $id = null): self
    {
        $validated = $request->validated();
        $validated['user_id'] = $request->user()->id ?? null;
        $validated['id'] = $id;

        return self::fromArray($validated);
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['user_id'] ?? null,
            $data['order_id'] ?? null,
            $data['description'] ?? null,
            $data['status'] ?? null
        );
    }
}
