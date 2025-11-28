<?php

declare(strict_types=1);

namespace Modules\Brand\DTOs;

use Illuminate\Http\Request;
use Modules\Brand\Models\Brand;
use Str;

readonly class BrandDTO
{
    public function __construct(
        public ?int $id,
        public ?string $title,
        public ?string $slug,
        public ?string $status,
        public ?string $created_at = null,
        public ?string $updated_at = null,
    ) {}

    public static function fromRequest(Request $request, ?int $id = null, ?Brand $existing = null): self
    {
        $data = $request->validated();

        // Support both 'name' and 'title' for backward compatibility
        $title = $data['title'] ?? $data['name'] ?? $existing?->title;

        return new self(
            $id,
            $title,
            $data['slug'] ?? $existing?->slug ?? Str::slug($title ?? 'brand'),
            $data['status'] ?? $existing?->status ?? 'active',
            $existing?->created_at?->toDateTimeString(),
            $existing?->updated_at?->toDateTimeString(),
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['title'] ?? null,
            $data['slug'] ?? null,
            $data['status'] ?? null,
            $data['created_at'] ?? null,
            $data['updated_at'] ?? null,
        );
    }
}
