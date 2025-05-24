<?php

declare(strict_types=1);

namespace Modules\Category\DTOs;

use Illuminate\Http\Request;

readonly class CategoryDTO
{
    public function __construct(
        public ?int $id,
        public ?string $name,
        public ?int $parent_id = null,
        public ?string $description = null
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
            $data['parent_id'] ?? null,
            $data['description'] ?? null
        );
    }
}
