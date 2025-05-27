<?php

declare(strict_types=1);

namespace Modules\Page\DTOs;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

readonly class PageDTO
{
    public function __construct(
        public ?int $id,
        public string $title,
        public string $slug,
        public string $content,
        public bool $is_active,
        public int $user_id,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['title'],
            $data['slug'],
            $data['content'],
            (bool) ($data['is_active'] ?? true),
            $data['user_id'],
            isset($data['created_at']) ? new Carbon($data['created_at']) : null,
            isset($data['updated_at']) ? new Carbon($data['updated_at']) : null,
        );
    }

    public static function fromRequest(Request $request, ?int $id = null): self
    {
        $validated = $request->validated();

        return self::fromArray([
            'id' => $id ?? ($validated['id'] ?? null),
            'title' => $validated['title'] ?? '',
            'slug' => $validated['slug'] ?? '',
            'content' => $validated['content'] ?? '',
            'is_active' => $validated['is_active'] ?? true,
            'user_id' => $validated['user_id'] ?? 0,
            'created_at' => $validated['created_at'] ?? null,
            'updated_at' => $validated['updated_at'] ?? null,
        ]);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'is_active' => $this->is_active,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }

    public function withId(int $id): self
    {
        return new self(
            $id,
            $this->title,
            $this->slug,
            $this->content,
            $this->is_active,
            $this->user_id,
            $this->created_at,
            $this->updated_at,
        );
    }
}
