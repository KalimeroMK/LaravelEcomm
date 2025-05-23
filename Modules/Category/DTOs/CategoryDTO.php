<?php

declare(strict_types=1);

namespace Modules\Category\DTOs;

use Illuminate\Http\Request;

readonly class CategoryDTO
{
    public function __construct(
        public ?int $id,
        public string $name,
        public ?int $parent_id = null,
        public ?string $description = null
    ) {}

    public static function fromRequest(Request $request, ?int $id = null): self
    {
        return new self(
            $id,
            $request->input('name'),
            $request->input('parent_id'),
            $request->input('description')
        );
    }
}
