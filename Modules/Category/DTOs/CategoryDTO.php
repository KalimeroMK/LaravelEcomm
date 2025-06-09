<?php

declare(strict_types=1);

namespace Modules\Category\DTOs;

use Illuminate\Http\Request;
use Modules\Category\Models\Category;

readonly class CategoryDTO
{
    public function __construct(
        public ?int $id,
        public ?string $title,
        public ?int $parent_id,
    ) {
    }

    public static function fromRequest(Request $request, ?int $id = null, ?Category $existing = null): self
    {
        $validated = $request->validated();

        return new self(
            id: $id,
            title: $validated['title'] ?? $existing?->title,
            parent_id: isset($validated['parent_id']) ? (int)$validated['parent_id'] : $existing?->parent_id,
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['title'] ?? null,
            isset($data['parent_id']) ? (int)$data['parent_id'] : null,
        );
    }
}
