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
        $validated = method_exists($request, 'validated') ? $request->validated() : [];
        $all = $request->all();

        return new self(
            id: $id ?? ($validated['id'] ?? $all['id'] ?? null),
            name: $validated['name'] ?? $all['name'] ?? '',
            permissions: $validated['permissions'] ?? $all['permissions'] ?? []
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
