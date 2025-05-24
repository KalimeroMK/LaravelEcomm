<?php

declare(strict_types=1);

namespace Modules\Product\DTOs;

use Illuminate\Http\Request;

readonly class ProductDTO
{
    public function __construct(
        public ?int $id,
        public ?string $title,
        public ?string $slug,
        public ?string $summary,
        public ?string $description,
        public ?int $stock,
        public ?string $status,
        public ?float $price,
        public ?float $discount = null,
        public ?bool $is_featured = null,
        public ?int $d_deal = null,
        public ?int $brand_id = null,
        public ?string $sku = null,
        public ?float $special_price = null,
        public ?string $special_price_start = null,
        public ?string $special_price_end = null,
        public ?string $created_at = null,
        public ?string $updated_at = null,
        public ?array $categories = null,
        public ?array $tags = null,
        public ?array $brand = null,
        public ?array $attributes = null,
        public ?array $author = null,
    ) {}

    public static function fromRequest(Request $request, ?int $id = null): self
    {
        return self::fromArray($request->validated() + ['id' => $id]);
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['title'] ?? null,
            $data['slug'] ?? null,
            $data['summary'] ?? null,
            $data['description'] ?? null,
            $data['stock'] ?? null,
            $data['status'] ?? null,
            $data['price'] ?? null,
            $data['discount'] ?? null,
            $data['is_featured'] ?? null,
            $data['d_deal'] ?? null,
            $data['brand_id'] ?? null,
            $data['sku'] ?? null,
            $data['special_price'] ?? null,
            $data['special_price_start'] ?? null,
            $data['special_price_end'] ?? null,
            $data['created_at'] ?? null,
            $data['updated_at'] ?? null,
            $data['categories'] ?? null,
            $data['tags'] ?? null,
            $data['brand'] ?? null,
            $data['attributes'] ?? null,
            $data['author'] ?? null,
        );
    }
}
