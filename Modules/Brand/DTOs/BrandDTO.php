<?php

declare(strict_types=1);

namespace Modules\Brand\DTOs;

use Illuminate\Http\Request;

readonly class BrandDTO
{
    public function __construct(
        public ?int $id,
        public ?string $title,
        public ?string $slug,
        public ?array $images = null,
        public ?string $status = null
    ) {
    }

    public static function fromRequest(Request $request, ?int $id = null): self
    {
        return self::fromArray($request->validated() + ['id' => $id]);
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['title'] ?? null,
            $data['images'] ?? null,
            $data['status'] ?? null,
            $data['slug'] ?? null
        );
    }
}
