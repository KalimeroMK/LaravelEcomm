<?php

declare(strict_types=1);

namespace Modules\Tag\DTOs;

use Illuminate\Support\Str;

class TagDto
{
    public string $title;

    public string $slug;

    public string $status;

    public function __construct(array $data)
    {
        $this->title = $data['title'];
        $this->slug = $data['slug'] ?? Str::slug($data['title']);
        $this->status = $data['status'] ?? 'active';
    }

    public static function fromRequest(array $data): self
    {
        return new self($data);
    }
}
