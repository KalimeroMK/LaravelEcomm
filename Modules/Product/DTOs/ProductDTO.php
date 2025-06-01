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
    ) {}

    public static function fromRequest(Request $request, ?int $id = null, ?Product $existing = null): self
    {
        $data = $request->validated();

        $specialPrice = $data['special_price'] ?? $existing?->special_price;
        if (is_array($specialPrice)) {
            $specialPrice = $specialPrice['amount'] ?? null;
        }

        return new self(
            id: $id,
            title: $data['title'] ?? $existing?->title,
            slug: $data['slug'] ?? $existing?->slug,
            summary: $data['summary'] ?? $existing?->summary,
            description: $data['description'] ?? $existing?->description,
            stock: $data['stock'] ?? $existing?->stock,
            status: $data['status'] ?? $existing?->status,
            price: $data['price'] ?? $existing?->price,
            discount: $data['discount'] ?? $existing?->discount,
            is_featured: $data['is_featured'] ?? $existing?->is_featured,
            d_deal: $data['d_deal'] ?? $existing?->d_deal,
            brand_id: $data['brand_id'] ?? $existing?->brand_id,
            sku: $data['sku'] ?? $existing?->sku,
            special_price: $specialPrice,
            special_price_start: $data['special_price_start'] ?? $existing?->special_price_start?->toDateTimeString(),
            special_price_end: $data['special_price_end'] ?? $existing?->special_price_end?->toDateTimeString(),
            created_at: $data['created_at'] ?? $existing?->created_at?->toDateTimeString(),
            updated_at: $data['updated_at'] ?? $existing?->updated_at?->toDateTimeString(),
            categories: $data['categories'] ?? [],
            tags: $data['tags'] ?? [],
        );
    }

    public static function fromArray(array $data): self
    {
        $specialPrice = $data['special_price'] ?? null;
        if (is_array($specialPrice)) {
            $specialPrice = $specialPrice['amount'] ?? null;
        }

        return new self(
            id: $data['id'] ?? null,
            title: $data['title'] ?? null,
            slug: $data['slug'] ?? null,
            summary: $data['summary'] ?? null,
            description: $data['description'] ?? null,
            stock: $data['stock'] ?? null,
            status: $data['status'] ?? null,
            price: $data['price'] ?? null,
            discount: $data['discount'] ?? null,
            is_featured: $data['is_featured'] ?? null,
            d_deal: $data['d_deal'] ?? null,
            brand_id: $data['brand_id'] ?? null,
            sku: $data['sku'] ?? null,
            special_price: $specialPrice,
            special_price_start: $data['special_price_start'] ?? null,
            special_price_end: $data['special_price_end'] ?? null,
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null,
            categories: $data['categories'] ?? [],
            tags: $data['tags'] ?? [],
        );
    }

    public static function fromModel(Product $product): self
    {
        return new self(
            id: $product->id,
            title: $product->title,
            slug: $product->slug,
            summary: $product->summary,
            description: $product->description,
            stock: $product->stock,
            status: $product->status,
            price: $product->price,
            discount: $product->discount,
            is_featured: $product->is_featured,
            d_deal: $product->d_deal,
            brand_id: $product->brand_id,
            sku: $product->sku,
            special_price: $product->special_price,
            special_price_start: $product->special_price_start?->toDateTimeString(),
            special_price_end: $product->special_price_end?->toDateTimeString(),
            created_at: $product->created_at?->toDateTimeString(),
            updated_at: $product->updated_at?->toDateTimeString(),
            categories: $product->categories()->pluck('id')->toArray(),
            tags: $product->tags()->pluck('id')->toArray(),
        );
    }
}
