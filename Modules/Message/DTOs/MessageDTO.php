<?php

declare(strict_types=1);

namespace Modules\Message\DTOs;

use Illuminate\Http\Request;

readonly class MessageDTO
{
    public function __construct(
        public ?int $id,
        public ?string $content,
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
            $data['content'] ?? null,
            $data['created_at'] ?? null
        );
    }
}
