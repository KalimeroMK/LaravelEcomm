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
        public ?string $email_verified_at = null,
        public ?string $created_at = null,
        public ?string $updated_at = null,
        /**
         * Plain-text password as submitted. Never persist this directly -
         * hash it at the point of write.
         */
        public ?string $password = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'] ?? null,
            email: $data['email'] ?? null,
            email_verified_at: $data['email_verified_at'] ?? null,
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null,
            password: $data['password'] ?? null,
        );
    }

    public static function fromRequest(Request $request, ?int $id = null): self
    {
        $validated = $request->validated();

        return self::fromArray([
            'id' => $id ?? ($validated['id'] ?? null),
            'name' => $validated['name'] ?? null,
            'email' => $validated['email'] ?? null,
            'email_verified_at' => $validated['email_verified_at'] ?? null,
            'password' => $validated['password'] ?? null,
        ]);
    }
}
