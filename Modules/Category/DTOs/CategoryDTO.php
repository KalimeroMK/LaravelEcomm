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
        public ?int $parent_id = null,
        public ?string $description = null
    ) {}

    public static function fromRequest(Request $request, ?int $id = null, ?Category $existing = null): self
    {
        $validated = $request->validated();

        return new self(
            id: $id,
            title: $validated['title'] ?? $existing?->title,
            parent_id: $validated['parent_id'] ?? $existing?->parent_id,
            description: $validated['description'] ?? $existing?->description,
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['title'] ?? null,
            $data['parent_id'] ?? null,
            $data['description'] ?? null
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'title' => $this->title,
            'parent_id' => $this->parent_id,
            'description' => $this->description,
        ], fn ($v) => $v !== null);
    }
}
