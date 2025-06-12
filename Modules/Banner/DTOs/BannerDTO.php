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
        public ?string $active_from = null,         // assuming string from request
        public ?string $active_to = null,           // same here
        public ?int $max_clicks = null,
        public ?int $max_impressions = null,
        public array $categories = [],
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
            $data['active_from'] ?? $existing?->active_from,
            $data['active_to'] ?? $existing?->active_to,
            $data['max_clicks'] ?? $existing?->max_clicks,
            $data['max_impressions'] ?? $existing?->max_impressions,
            $data['categories'] ?? ($existing?->categories?->pluck('id')->toArray() ?? []),
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
            $data['active_from'] ?? null,
            $data['active_to'] ?? null,
            $data['max_clicks'] ?? null,
            $data['max_impressions'] ?? null,
            $data['categories'] ?? [],
        );
    }
}
