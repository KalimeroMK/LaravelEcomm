<?php

declare(strict_types=1);

namespace Modules\Post\DTOs;

use Auth;
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
        public array $tags = [],
    ) {}

    public static function fromRequest(Request $request, ?int $id = null, ?Post $post = null): self
    {
        $data = $request->validated();

        return new self(
            id: $id,
            title: $data['title'] ?? $post?->title ?? '',
            slug: $data['slug'] ?? $post?->slug ?? '',
            summary: $data['summary'] ?? $post?->summary ?? '',
            description: $data['description'] ?? $post?->description,
            status: $data['status'] ?? $post?->status ?? '',
            user_id: Auth::id() ?? $post?->user_id ?? 1,
            categories: array_filter((array) ($data['categories'] ?? [])),
            tags: array_filter((array) ($data['tags'] ?? [])),
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            title: $data['title'] ?? '',
            slug: $data['slug'] ?? '',
            summary: $data['summary'] ?? '',
            description: $data['description'] ?? null,
            status: $data['status'] ?? '',
            user_id: $data['user_id'] ?? 1,
            categories: array_filter((array) ($data['categories'] ?? [])),
            tags: array_filter((array) ($data['tags'] ?? [])),
        );
    }

    public static function fromModel(Post $post): self
    {
        return new self(
            id: $post->id,
            title: $post->title,
            slug: $post->slug,
            summary: $post->summary,
            description: $post->description,
            status: $post->status,
            user_id: $post->user_id,
            categories: $post->categories()->pluck('id')->toArray(),
            tags: $post->tags()->pluck('id')->toArray(),
        );
    }
}
