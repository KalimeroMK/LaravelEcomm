<?php

declare(strict_types=1);

namespace Modules\Attribute\DTOs;

use Illuminate\Http\Request;

readonly class AttributeGroupDTO
{
    public function __construct(
        public ?int $id,
        public string $name,
        public ?array $attributes = []
    ) {}

    public static function fromRequest(Request $request): self
    {
        $data = $request->validated();

        return new self(
            $data['id'] ?? null,
            $data['name'],
            $data['attributes'] ?? []
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'attributes' => $this->attributes,
        ];
    }

    public function withId(int $id): self
    {
        return new self($id, $this->name, $this->attributes);
    }
}
