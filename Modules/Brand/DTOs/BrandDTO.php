<?php

declare(strict_types=1);

namespace Modules\Brand\DTOs;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

readonly class BrandDTO
{
    public function __construct(
        public ?int $id,
        public ?string $title,
        public ?string $slug,
        public ?string $status,
        public ?array $images = null,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
        public ?int $media_count = null,
    ) {}

    public static function fromRequest(Request $request, ?int $id = null): self
    {
        return self::fromArray($request->validated() + ['id' => $id]);
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['title'] ?? null,
            $data['slug'] ?? null,
            $data['status'] ?? null,
            $data['images'] ?? null,
            isset($data['created_at']) ? new Carbon($data['created_at']) : null,
            isset($data['updated_at']) ? new Carbon($data['updated_at']) : null,
            $data['media_count'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'status' => $this->status,
            'images' => $this->images,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'media_count' => $this->media_count,
            'products_count' => $this->products_count,
        ];
    }

    public function withId(int $id): self
    {
        return new self(
            $id,
            $this->title,
            $this->slug,
            $this->status,
            $this->images,
            $this->created_at,
            $this->updated_at,
            $this->media_count,
            $this->products_count,
        );
    }
}
