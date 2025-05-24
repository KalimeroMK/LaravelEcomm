<?php

declare(strict_types=1);

namespace Modules\User\DTOs;

use Illuminate\Http\Request;

readonly class UserDTO
{
    public function __construct(
        public ?int $id,
        public ?string $name,
        public ?string $email,
        public ?string $status = null,
        public ?string $email_verified_at = null,
        public ?string $created_at = null,
        public ?string $updated_at = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['name'] ?? null,
            $data['email'] ?? null,
            $data['status'] ?? null,
            $data['email_verified_at'] ?? null,
            $data['created_at'] ?? null,
            $data['updated_at'] ?? null,
        );
    }

    public static function fromRequest(Request $request, ?int $id = null): self
    {
        $validated = $request->validated();

        return self::fromArray([
            'id' => $id ?? ($validated['id'] ?? null),
            'name' => $validated['name'] ?? null,
            'email' => $validated['email'] ?? null,
            'status' => $validated['status'] ?? null,
            'email_verified_at' => $validated['email_verified_at'] ?? null,
        ]);
    }
}
