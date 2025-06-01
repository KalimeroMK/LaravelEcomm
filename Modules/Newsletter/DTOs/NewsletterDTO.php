<?php

declare(strict_types=1);

namespace Modules\Newsletter\DTOs;

use Illuminate\Http\Request;
use Modules\Newsletter\Models\Newsletter;

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

    public static function fromRequest(Request $request, ?int $id = null, ?Newsletter $existing = null): self
    {
        $data = $request->validated();

        return new self(
            $id,
            $data['email'] ?? $existing?->email,
            $data['token'] ?? $existing?->token,
            $data['is_validated'] ?? $existing?->is_validated,
            $data['created_at'] ?? $existing?->created_at?->toDateTimeString(),
            $data['updated_at'] ?? $existing?->updated_at?->toDateTimeString()
        );
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
