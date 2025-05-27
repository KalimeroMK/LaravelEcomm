<?php

declare(strict_types=1);

namespace Modules\Tag\DTOs;

class TagDto
{
    public string $title;

    public string $slug;

    public string $status;

    public function __construct(array $data)
    {
        $this->title = $data['title'];
        $this->slug = $data['slug'];
        $this->status = $data['status'];
    }

    public static function fromRequest(array $data): self
    {
        return new self($data);
    }
}
