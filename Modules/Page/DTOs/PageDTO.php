<?php

declare(strict_types=1);

namespace Modules\Page\DTOs;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Modules\Page\Models\Page;

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

    public static function fromRequest(Request $request, ?int $id = null, ?Page $existing = null): self
    {
        $data = $request->validated();

        return new self(
            $id,
            $data['title'] ?? $existing?->title ?? '',
            $data['slug'] ?? $existing?->slug ?? '',
            $data['content'] ?? $existing?->content ?? '',
            (bool) ($data['is_active'] ?? $existing?->is_active ?? true),
            $data['user_id'] ?? $existing?->user_id ?? 0,
            isset($data['created_at']) ? new Carbon($data['created_at']) : $existing?->created_at,
            isset($data['updated_at']) ? new Carbon($data['updated_at']) : $existing?->updated_at,
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['title'] ?? '',
            $data['slug'] ?? '',
            $data['content'] ?? '',
            (bool) ($data['is_active'] ?? true),
            $data['user_id'] ?? 0,
            isset($data['created_at']) ? new Carbon($data['created_at']) : null,
            isset($data['updated_at']) ? new Carbon($data['updated_at']) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'is_active' => $this->is_active,
            'user_id' => $this->user_id,
        ];
    }
}
