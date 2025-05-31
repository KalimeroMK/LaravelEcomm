<?php

declare(strict_types=1);

namespace Modules\Newsletter\DTOs;

use Illuminate\Http\Request;

readonly class NewsletterDTO
{
    public function __construct(
        public ?int $id,
        public ?string $email,
        public ?string $token,
        public ?bool $is_validated,
        public ?string $created_at,
        public ?string $updated_at

    ) {}

    public static function fromRequest(Request $request, ?int $id = null): self
    {
        return self::fromArray($request->validated() + ['id' => $id]);
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['email'] ?? null,
            $data['token'] ?? null,
            $data['is_validated'] ?? null,
            $data['created_at'] ?? null,
            $data['updated_at'] ?? null

        );
    }
}
