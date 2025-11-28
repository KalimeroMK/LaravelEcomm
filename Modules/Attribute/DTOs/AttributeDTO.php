<?php

declare(strict_types=1);

namespace Modules\Attribute\DTOs;

use Illuminate\Http\Request;
use Modules\Attribute\Models\Attribute;

readonly class AttributeDTO
{
    public function __construct(
        public ?int $id,
        public string $name,
        public ?string $code,
        public ?string $type,
        public ?string $display,
        public bool $is_required = false,
        public bool $is_filterable = false,
        public bool $is_configurable = false,
        public ?string $created_at = null,
        public ?string $updated_at = null,
    ) {}

    public static function fromRequest(Request $request, ?int $id = null, ?Attribute $existing = null): self
    {
        $data = $request->all();

        return new self(
            $id,
            $data['name'] ?? ($existing !== null ? $existing->name : ''),
            $data['code'] ?? ($existing !== null ? $existing->code : null),
            $data['type'] ?? ($existing !== null ? $existing->type : null),
            $data['display'] ?? ($existing !== null ? $existing->display : null),
            (bool) ($data['is_required'] ?? ($existing !== null ? (bool) $existing->is_required : false)),
            (bool) ($data['filterable'] ?? $data['is_filterable'] ?? ($existing !== null ? (bool) $existing->is_filterable : false)),
            (bool) ($data['configurable'] ?? $data['is_configurable'] ?? ($existing !== null ? (bool) $existing->is_configurable : false)),
            $existing?->created_at?->toDateTimeString(),
            $existing?->updated_at?->toDateTimeString(),
        );
    }

    public static function fromModel(Attribute $attribute): self
    {
        return new self(
            $attribute->id,
            $attribute->name,
            $attribute->code,
            $attribute->type,
            $attribute->display,
            (bool) $attribute->is_required,
            (bool) $attribute->is_filterable,
            (bool) $attribute->is_configurable,
            $attribute->created_at?->toDateTimeString(),
            $attribute->updated_at?->toDateTimeString(),
        );
    }
}
