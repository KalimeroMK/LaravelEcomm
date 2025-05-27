<?php

declare(strict_types=1);

namespace Modules\Post\DTOs;

use Illuminate\Http\Request;

readonly class PostDTO
{
    public function __construct(
        public ?int $id,
        public string $title,
        public string $slug,
        public string $summary,
        public ?string $description,
        public ?string $photo,
        public string $status,
        public int $user_id,
        public array $categories = [],
        public array $tags = []
    ) {}

    public static function fromRequest(Request $request, ?int $id = null): self
    {
        return self::fromArray($request->validated() + ['id' => $id]);
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['title'] ?? '',
            $data['slug'] ?? '',
            $data['summary'] ?? '',
            $data['description'] ?? null,
            $data['photo'] ?? null,
            $data['status'] ?? '',
            $data['user_id'] ?? 0,
            $data['categories'] ?? [],
            $data['tags'] ?? []
        );
    }
}
