<?php

declare(strict_types=1);

namespace Modules\Attribute\DTOs;

use Illuminate\Http\Request;
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
        public ?bool $filterable = false,
        public ?bool $configurable = false,
        public array $options = []
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
            (bool)($data['filterable'] ?? false),
            (bool)($data['configurable'] ?? false),
            $data['options'] ?? []
        );
    }

    public function withId(int $id): self
    {
        return new self(
            $id,
            $this->name,
            $this->code,
            $this->type,
            $this->display,
            $this->filterable,
            $this->configurable,
            $this->options
        );
    }
}
