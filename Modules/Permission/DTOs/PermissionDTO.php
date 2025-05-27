<?php

declare(strict_types=1);

namespace Modules\Permission\DTOs;

use Illuminate\Http\Request;

readonly class PermissionDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $guard_name,
        public string $created_at
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['name'],
            $data['guard_name'],
            isset($data['created_at']) ? (string) $data['created_at'] : now()->toDateTimeString()
        );
    }

    public static function fromRequest(Request $request, ?int $id = null): self
    {
        $validated = $request->validated();

        return self::fromArray([
            'id' => $id ?? ($validated['id'] ?? 0),
            'name' => $validated['name'] ?? '',
            'guard_name' => $validated['guard_name'] ?? '',
            'created_at' => $validated['created_at'] ?? now()->toDateTimeString(),
        ]);
    }
}
