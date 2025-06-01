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
        public ?string $status = null,
        public ?string $created_at = null,
        public ?string $updated_at = null,
    ) {}

    public static function fromRequest(Request $request, ?int $id = null, ?Attribute $existing = null): self
    {
        $data = $request->validated();

        return new self(
            $id,
            $data['name'] ?? $existing?->name,
            $data['code'] ?? $existing?->code,
            $data['type'] ?? $existing?->type,
            $data['display'] ?? $existing?->display,
            (bool) ($data['is_required'] ?? $existing?->is_required ?? false),
            (bool) ($data['is_filterable'] ?? $existing?->is_filterable ?? false),
            (bool) ($data['is_configurable'] ?? $existing?->is_configurable ?? false),
            $data['status'] ?? $existing?->status,
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
            $attribute->is_required,
            $attribute->is_filterable,
            $attribute->is_configurable,
            $attribute->status,
            $attribute->created_at?->toDateTimeString(),
            $attribute->updated_at?->toDateTimeString(),
        );
    }
}
