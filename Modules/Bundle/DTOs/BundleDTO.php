<?php

declare(strict_types=1);

namespace Modules\Bundle\DTOs;

use Illuminate\Http\Request;

readonly class BundleDTO
{
    public function __construct(
        public ?int $id,
        public string $name,
        public ?string $description = null,
        public ?array $images = null,
        public array $products = [],
        public ?float $price = null,
        public array $extra = []
    ) {}

    public static function fromRequest(Request $request): self
    {
        $data = $request->validated();

        return new self(
            $data['id'] ?? null,
            $data['name'],
            $data['description'] ?? null,
            $data['images'] ?? null,
            $data['products'] ?? [],
            isset($data['price']) ? (float) ($data['price']) : null,
            $data['extra'] ?? []
        );
    }

    public function withId(int $id): self
    {
        return new self(
            $id,
            $this->name,
            $this->description,
            $this->images,
            $this->products,
            $this->price,
            $this->extra
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'image' => $this->images,
            'products' => $this->products,
            'price' => $this->price,
            'extra' => $this->extra,
        ];
    }
}
