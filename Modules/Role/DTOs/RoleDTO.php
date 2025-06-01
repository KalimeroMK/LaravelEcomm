<?php

declare(strict_types=1);

namespace Modules\Role\DTOs;

use Illuminate\Http\Request;

readonly class RoleDTO
{
    public function __construct(
        public ?int $id,
        public string $name,
        public array $permissions = []
    ) {}

    public static function fromRequest(Request $request, ?int $id = null): self
    {
        $validated = $request->validated();

        return new self(
            id: $id ?? $validated['id'] ?? null,
            name: $validated['name'] ?? '',
            permissions: $validated['permissions'] ?? []
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'] ?? '',
            permissions: $data['permissions'] ?? []
        );
    }
}
