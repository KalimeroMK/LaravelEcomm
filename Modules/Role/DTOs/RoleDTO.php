<?php

declare(strict_types=1);

namespace Modules\Role\DTOs;

use Illuminate\Http\Request;

readonly class RoleDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public array $permissions = []
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['name'],
            $data['permissions'] ?? []
        );
    }

    public static function fromRequest(Request $request, ?int $id = null): self
    {
        $validated = $request->validated();

        return self::fromArray([
            'id' => $id ?? ($validated['id'] ?? 0),
            'name' => $validated['name'] ?? '',
            'permissions' => $validated['permissions'] ?? [],
        ]);
    }
}
