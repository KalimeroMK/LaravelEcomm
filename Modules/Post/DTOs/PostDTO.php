<?php

declare(strict_types=1);

namespace Modules\Post\DTOs;

use Illuminate\Http\Request;
use Modules\Post\Models\Post;

readonly class PostDTO
{
    public function __construct(
        public ?int $id,
        public string $title,
        public string $slug,
        public string $summary,
        public ?string $description,
        public string $status,
        public int $user_id,
        public array $categories = [],
        public array $tags = []
    ) {}

    public static function fromRequest(Request $request, ?int $id = null, ?Post $post = null): self
    {
        $data = $request->validated();

        return new self(
            $id,
            $data['title'] ?? $post?->title ?? '',
            $data['slug'] ?? $post?->slug ?? '',
            $data['summary'] ?? $post?->summary ?? '',
            $data['description'] ?? $post?->description ?? null,
            $data['status'] ?? $post?->status ?? '',
            $request->input('user_id') ?? $post?->user_id ?? 0,
            $data['categories'] ?? [],
            $data['tags'] ?? []
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['title'] ?? '',
            $data['slug'] ?? '',
            $data['summary'] ?? '',
            $data['description'] ?? null,
            $data['status'] ?? '',
            $data['user_id'] ?? 0,
            $data['categories'] ?? [],
            $data['tags'] ?? []
        );
    }
}
