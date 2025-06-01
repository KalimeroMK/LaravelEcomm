<?php

declare(strict_types=1);

namespace Modules\Bundle\DTOs;

use Illuminate\Http\Request;
use Modules\Bundle\Http\Requests\Store;
use Modules\Bundle\Http\Requests\Update;
use Modules\Bundle\Models\Bundle;

readonly class BundleDTO
{
    public function __construct(
        public ?int $id,
        public ?string $name,
        public ?string $description,
        public ?float $price,
        public ?array $products = [],
        public ?array $images = [],
        public ?string $extra = null,
    ) {}

    public static function fromRequest(Store|Update|Request $request, ?int $id = null, ?Bundle $bundle = null): self
    {
        return self::fromArray($request->validated() + ['id' => $id]);
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['name'] ?? null,
            $data['description'] ?? null,
            $data['price'] ?? null,
            $data['products'] ?? [],
            $data['images'] ?? [],
            $data['extra'] ?? null,
        );
    }

    public function withId(int $id): self
    {
        return new self(
            $id,
            $this->name,
            $this->description,
            $this->price,
            $this->products,
            $this->images,
            $this->extra,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'products' => $this->products,
            'images' => $this->images,
            'extra' => $this->extra,
        ];
    }
}
