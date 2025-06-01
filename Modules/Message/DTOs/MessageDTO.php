<?php

declare(strict_types=1);

namespace Modules\Message\DTOs;

use Illuminate\Http\Request;
use Modules\Message\Models\Message;

readonly class MessageDTO
{
    public function __construct(
        public ?int $id,
        public ?string $name,
        public ?string $subject,
        public ?string $email,
        public ?string $phone,
        public ?string $message,
        public ?string $read_at,
    ) {}

    public static function fromRequest(Request $request, ?int $id = null, ?Message $existing = null): self
    {
        $data = $request->validated();

        return new self(
            $id,
            $data['name'] ?? $existing?->name,
            $data['subject'] ?? $existing?->subject,
            $data['email'] ?? $existing?->email,
            $data['phone'] ?? $existing?->phone,
            $data['message'] ?? $existing?->message,
            $data['read_at'] ?? $existing?->read_at,
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['name'] ?? null,
            $data['subject'] ?? null,
            $data['email'] ?? null,
            $data['phone'] ?? null,
            $data['message'] ?? null,
            $data['read_at'] ?? null,
        );
    }
}
