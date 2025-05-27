<?php

declare(strict_types=1);

namespace Modules\Notification\DTOs;

use Illuminate\Http\Request;

readonly class NotificationDTO
{
    public function __construct(
        public ?int $id = null,
        public ?string $type = null,
        public ?string $notifiable_type = null,
        public ?int $notifiable_id = null,
        public ?string $data = null,
        public ?string $read_at = null,
        public ?string $created_at = null,
        public ?string $updated_at = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['type'] ?? null,
            $data['notifiable_type'] ?? null,
            $data['notifiable_id'] ?? null,
            $data['data'] ?? null,
            $data['read_at'] ?? null,
            $data['created_at'] ?? null,
            $data['updated_at'] ?? null,
        );
    }

    public static function fromRequest(Request $request, ?int $id = null): self
    {
        $validated = $request->validated();

        return self::fromArray([
            'id' => $id ?? ($validated['id'] ?? null),
            'type' => $validated['type'] ?? null,
            'notifiable_type' => $validated['notifiable_type'] ?? null,
            'notifiable_id' => $validated['notifiable_id'] ?? null,
            'data' => $validated['data'] ?? null,
            'read_at' => $validated['read_at'] ?? null,
        ]);
    }
}
