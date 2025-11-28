<?php

declare(strict_types=1);

namespace Modules\Attribute\DTOs;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

readonly class AttributeOptionDTO
{
    public function __construct(
        public ?int $id,
        public int $attribute_id,
        public string $value,
        public ?string $label = null,
        public int $sort_order = 0,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        if (method_exists($request, 'validated')) {
            $data = $request->validated();
        } else {
            $data = $request->all();
        }

        return self::fromArray($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['attribute_id'],
            $data['value'],
            $data['label'] ?? null,
            $data['sort_order'] ?? 0,
            isset($data['created_at']) ? new Carbon($data['created_at']) : null,
            isset($data['updated_at']) ? new Carbon($data['updated_at']) : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'attribute_id' => $this->attribute_id,
            'value' => $this->value,
            'label' => $this->label,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }

    public function withId(int $id): self
    {
        return new self(
            $id,
            $this->attribute_id,
            $this->value,
            $this->label,
            $this->sort_order,
            $this->created_at,
            $this->updated_at,
        );
    }
}
