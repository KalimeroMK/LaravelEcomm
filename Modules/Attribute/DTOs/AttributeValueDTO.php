<?php

declare(strict_types=1);

namespace Modules\Attribute\DTOs;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

readonly class AttributeValueDTO
{
    public function __construct(
        public ?int $id,
        public int $product_id,
        public int $attribute_id,
        public ?string $text_value = null,
        public ?bool $boolean_value = null,
        public ?Carbon $date_value = null,
        public ?int $integer_value = null,
        public ?float $float_value = null,
        public ?string $string_value = null,
        public ?string $url_value = null,
        public ?string $hex_value = null,
        public ?float $decimal_value = null,
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
            $data['product_id'],
            $data['attribute_id'],
            $data['text_value'] ?? null,
            $data['boolean_value'] ?? null,
            isset($data['date_value']) ? new Carbon($data['date_value']) : null,
            $data['integer_value'] ?? null,
            $data['float_value'] ?? null,
            $data['string_value'] ?? null,
            $data['url_value'] ?? null,
            $data['hex_value'] ?? null,
            $data['decimal_value'] ?? null,
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
            'product_id' => $this->product_id,
            'attribute_id' => $this->attribute_id,
            'text_value' => $this->text_value,
            'boolean_value' => $this->boolean_value,
            'date_value' => $this->date_value?->toDateTimeString(),
            'integer_value' => $this->integer_value,
            'float_value' => $this->float_value,
            'string_value' => $this->string_value,
            'url_value' => $this->url_value,
            'hex_value' => $this->hex_value,
            'decimal_value' => $this->decimal_value,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }

    public function withId(int $id): self
    {
        return new self(
            $id,
            $this->product_id,
            $this->attribute_id,
            $this->text_value,
            $this->boolean_value,
            $this->date_value,
            $this->integer_value,
            $this->float_value,
            $this->string_value,
            $this->url_value,
            $this->hex_value,
            $this->decimal_value,
            $this->created_at,
            $this->updated_at,
        );
    }
}
