<?php

declare(strict_types=1);

namespace Modules\Attribute\DTOs;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Modules\Attribute\Http\Requests\Attribute\Store;
use Modules\Attribute\Http\Requests\Attribute\Update;

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
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
        public array $options = [],
        public ?int $options_count = null,
    ) {
    }

    public static function fromRequest(Store|Update|Request $request): self
    {
        $data = $request->validated();
        return self::fromArray($data);
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['name'],
            $data['code'] ?? null,
            $data['type'] ?? null,
            $data['display'] ?? null,
            (bool)($data['is_required'] ?? false),
            (bool)($data['is_filterable'] ?? false),
            (bool)($data['is_configurable'] ?? false),
            isset($data['created_at']) ? new Carbon($data['created_at']) : null,
            isset($data['updated_at']) ? new Carbon($data['updated_at']) : null,
            $data['options'] ?? [],
            $data['options_count'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'type' => $this->type,
            'display' => $this->display,
            'is_required' => $this->is_required,
            'is_filterable' => $this->is_filterable,
            'is_configurable' => $this->is_configurable,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'options' => $this->options,
            'options_count' => $this->options_count,
        ];
    }

    public function withId(int $id): self
    {
        return new self(
            $id,
            $this->name,
            $this->code,
            $this->type,
            $this->display,
            $this->is_required,
            $this->is_filterable,
            $this->is_configurable,
            $this->created_at,
            $this->updated_at,
            $this->options,
            $this->options_count,
        );
    }
}
