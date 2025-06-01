<?php

declare(strict_types=1);

namespace Modules\Banner\DTOs;

use Illuminate\Http\Request;
use Modules\Banner\Models\Banner;

readonly class BannerDTO
{
    public function __construct(
        public ?int $id,
        public ?string $title,
        public ?string $slug,
        public ?string $description,
        public ?string $status,
    ) {}

    public static function fromRequest(Request $request, ?int $id = null, ?Banner $existing = null): self
    {
        $data = $request->validated();

        return new self(
            $id,
            $data['title'] ?? $existing?->title,
            $data['slug'] ?? $existing?->slug,
            $data['description'] ?? $existing?->description,
            $data['status'] ?? $existing?->status,
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['title'] ?? null,
            $data['slug'] ?? null,
            $data['description'] ?? null,
            $data['status'] ?? null,
        );
    }
}
