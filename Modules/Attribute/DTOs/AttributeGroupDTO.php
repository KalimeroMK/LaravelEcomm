<?php

declare(strict_types=1);

namespace Modules\Attribute\DTOs;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

readonly class AttributeGroupDTO
{
    public function __construct(
        public ?int $id,
        public ?string $name,
        public ?array $attributes = [],
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
        public ?int $attributes_count = null,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return self::fromArray($request->validated());
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['name'] ?? null,
            $data['attributes'] ?? [],
            isset($data['created_at']) ? new Carbon($data['created_at']) : null,
            isset($data['updated_at']) ? new Carbon($data['updated_at']) : null,
            $data['attributes_count'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'attributes' => $this->attributes,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'attributes_count' => $this->attributes_count,
        ];
    }

    public function withId(int $id): self
    {
        return new self(
            $id,
            $this->name,
            $this->attributes,
            $this->created_at,
            $this->updated_at,
            $this->attributes_count,
        );
    }
}
