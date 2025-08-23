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
        public ?bool $is_validated
    ) {}

    public static function fromRequest(Request $request, ?int $id = null, ?Newsletter $existing = null): self
    {
        $data = $request->validated();

        return new self(
            $id,
            $data['email'] ?? $existing?->email,
            $data['token'] ?? $existing?->token,
            $data['is_validated'] ?? $existing?->is_validated
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['email'] ?? null,
            $data['token'] ?? null,
            $data['is_validated'] ?? null
        );
    }
}
