<?php

declare(strict_types=1);

namespace Modules\Language\DTOs;

readonly class LanguageDTO
{
    public function __construct(
        public string $code,
        public string $name,
        public string $nativeName,
        public ?string $flag,
        public string $direction,
        public int $sortOrder,
        public bool $isActive,
        public bool $isDefault,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            code: $data['code'],
            name: $data['name'],
            nativeName: $data['native_name'],
            flag: $data['flag'] ?? null,
            direction: $data['direction'],
            sortOrder: $data['sort_order'] ?? 0,
            isActive: $data['is_active'] ?? true,
            isDefault: $data['is_default'] ?? false,
        );
    }

    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'native_name' => $this->nativeName,
            'flag' => $this->flag,
            'direction' => $this->direction,
            'sort_order' => $this->sortOrder,
            'is_active' => $this->isActive,
            'is_default' => $this->isDefault,
        ];
    }
}
