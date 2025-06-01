<?php

declare(strict_types=1);

namespace Modules\Brand\DTOs;

use Illuminate\Http\Request;
use Modules\Brand\Models\Brand;

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

        return new self(
            $id,
            $data['title'] ?? $existing?->title,
            $data['slug'] ?? $existing?->slug,
            $data['status'] ?? $existing?->status,
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
