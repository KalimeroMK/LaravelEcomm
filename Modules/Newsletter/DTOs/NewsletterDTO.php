<?php

declare(strict_types=1);

namespace Modules\Newsletter\DTOs;

use Modules\Newsletter\Models\Newsletter;
use Illuminate\Http\Request;

readonly class NewsletterDTO
{
    public function __construct(
        public ?int $id,
        public ?string $email,
        public ?string $created_at = null
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
            $data['created_at'] ?? null
        );
    }

    public static function fromModel(Newsletter $newsletter): self
    {
        return new self(
            $newsletter->id,
            $newsletter->email,
            $newsletter->created_at->toDateTimeString()
        );
    }
}
