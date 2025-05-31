<?php

declare(strict_types=1);

namespace Modules\Product\DTOs;

use Illuminate\Http\Request;
use Modules\Product\Models\Product;

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
    ) {}

    public static function fromRequest(Request $request, ?int $id = null, ?Product $existing = null): self
    {
        $data = $request->validated();

        return new self(
            $id,
            $data['title'] ?? $existing?->title,
            $data['slug'] ?? $existing?->slug,
            $data['summary'] ?? $existing?->summary,
            $data['description'] ?? $existing?->description,
            $data['stock'] ?? $existing?->stock,
            $data['status'] ?? $existing?->status,
            $data['price'] ?? $existing?->price,
            $data['discount'] ?? $existing?->discount,
            $data['is_featured'] ?? $existing?->is_featured,
            $data['d_deal'] ?? $existing?->d_deal,
            $data['brand_id'] ?? $existing?->brand_id,
            $data['sku'] ?? $existing?->sku,
            $data['special_price'] ?? $existing?->special_price,
            $data['special_price_start'] ?? $existing?->special_price_start?->toDateTimeString(),
            $data['special_price_end'] ?? $existing?->special_price_end?->toDateTimeString(),
            $data['created_at'] ?? $existing?->created_at?->toDateTimeString(),
            $data['updated_at'] ?? $existing?->updated_at?->toDateTimeString(),
            $data['categories'] ?? $existing?->categories?->pluck('id')->toArray(),
            $data['tags'] ?? $existing?->tags?->pluck('id')->toArray(),
            $existing?->brand ? $existing->brand->toArray() : null,
            $existing?->attributes ? $existing->attributes->toArray() : null,
        );
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
        );
    }

    public static function fromModel(Product $product): self
    {
        return new self(
            $product->id,
            $product->title,
            $product->slug,
            $product->summary,
            $product->description,
            $product->stock,
            $product->status,
            $product->price,
            $product->discount,
            $product->is_featured,
            $product->d_deal,
            $product->brand_id,
            $product->sku,
            $product->special_price,
            $product->special_price_start,
            $product->special_price_end,
            $product->created_at,
            $product->updated_at,
            $product->categories?->pluck('id')->toArray(),
            $product->tags?->pluck('id')->toArray(),
            $product->brand ? $product->brand->toArray() : null,
            $product->attributes ? $product->attributes->toArray() : null,
        );
    }
}
