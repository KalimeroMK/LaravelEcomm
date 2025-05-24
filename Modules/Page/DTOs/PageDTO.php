<?php

declare(strict_types=1);

namespace Modules\Page\DTOs;

use Illuminate\Http\Request;

readonly class PageDTO
{
    public function __construct(
        public int $id,
        public string $title,
        public string $content,
        public string $created_at
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['title'],
            $data['content'],
            isset($data['created_at']) ? (string)$data['created_at'] : now()->toDateTimeString()
        );
    }

    public static function fromRequest(Request $request, ?int $id = null): self
    {
        $validated = $request->validated();

        return self::fromArray([
            'id' => $id ?? ($validated['id'] ?? 0),
            'title' => $validated['title'] ?? '',
            'content' => $validated['content'] ?? '',
            'created_at' => $validated['created_at'] ?? now()->toDateTimeString(),
        ]);
    }
}
