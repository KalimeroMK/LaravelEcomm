<?php

declare(strict_types=1);

namespace Modules\Tag\DTOs;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

readonly class TagDto
{
    public function __construct(
        public ?int $id,
        public string $title,
        public string $slug,
        public string $status,
    ) {}

    public static function fromRequest(Request $request, ?int $id = null): self
    {
        $data = $request->validated();

        return new self(
            id: $id,
            title: $data['title'] ?? '',
            slug: $data['slug'] ?? Str::slug($data['title'] ?? ''),
            status: $data['status'] ?? 'active',
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            title: $data['title'] ?? '',
            slug: $data['slug'] ?? Str::slug($data['title'] ?? ''),
            status: $data['status'] ?? 'active',
        );
    }
}
