<?php

declare(strict_types=1);

namespace Modules\Message\DTOs;

use Illuminate\Http\Request;

readonly class MessageDTO
{
    public function __construct(
        public ?int $id,
        public ?string $name,
        public ?string $subject,
        public ?string $email,
        public ?string $photo,
        public ?string $phone,
        public ?string $message,
        public ?string $read_at,
    ) {}

    public static function fromRequest(Request $request, ?int $id = null): self
    {
        return self::fromArray($request->validated() + ['id' => $id]);
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['name'] ?? null,
            $data['subject'] ?? null,
            $data['email'] ?? null,
            $data['photo'] ?? null,
            $data['phone'] ?? null,
            $data['message'] ?? null,
            $data['read_at'] ?? null,
        );
    }

    public function withId(int $id): self
    {
        return new self(
            $id,
            $this->name,
            $this->subject,
            $this->email,
            $this->photo,
            $this->phone,
            $this->message,
            $this->read_at,
        );
    }
}
