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
        public ?int $attribute_set_id = null,
        public ?string $sku = null,
        public ?float $special_price = null,
        public ?string $special_price_start = null,
        public ?string $special_price_end = null,
        public ?string $created_at = null,
        public ?string $updated_at = null,
        public ?array $categories = null,
        public ?array $tags = null,
        public ?array $attributes = null,
    ) {}

    public static function fromRequest(Request $request, ?int $id = null, ?Product $existing = null): self
    {
        $data = $request->validated();

        $specialPrice = $data['special_price'] ?? null;
        if (is_array($specialPrice)) {
            $specialPrice = $specialPrice['amount'] ?? null;
        }

        $normalizedAttributes = [];

        // Fix: Always normalize attributes to ['value' => ...] for SyncProductAttributesAction
        foreach ($data['attributes'] ?? [] as $key => $optionValue) {
            // Check if there is a custom value for this attribute
            if (isset($data['attributes_custom'][$key]) && $optionValue === 'custom') {
                $normalizedAttributes[$key] = [
                    'value' => $data['attributes_custom'][$key],
                ];
            } else {
                $normalizedAttributes[$key] = [
                    'value' => $optionValue,
                ];
            }
        }

        return new self(
            id: $id,
            title: $data['title'] ?? $existing?->title,
            slug: $data['slug'] ?? $existing?->slug,
            summary: $data['summary'] ?? $existing?->summary,
            description: $data['description'] ?? $existing?->description,
            stock: isset($data['stock']) ? (int) $data['stock'] : $existing?->stock,
            status: $data['status'] ?? $existing?->status,
            price: isset($data['price']) ? (float) $data['price'] : $existing?->price,
            discount: isset($data['discount']) ? (float) $data['discount'] : $existing?->discount,
            is_featured: isset($data['is_featured']) ? (bool) $data['is_featured'] : $existing?->is_featured,
            d_deal: isset($data['d_deal']) ? (int) $data['d_deal'] : $existing?->d_deal,
            brand_id: isset($data['brand_id']) ? (int) $data['brand_id'] : $existing?->brand_id,
            attribute_set_id: isset($data['attribute_set_id']) ? (int) $data['attribute_set_id'] : $existing?->attribute_set_id,
            sku: $data['sku'] ?? $existing?->sku,
            special_price: isset($specialPrice) ? (float) $specialPrice : null,
            special_price_start: $data['special_price_start'] ?? $existing?->special_price_start?->toDateTimeString(),
            special_price_end: $data['special_price_end'] ?? $existing?->special_price_end?->toDateTimeString(),
            created_at: $data['created_at'] ?? $existing?->created_at?->toDateTimeString(),
            updated_at: $data['updated_at'] ?? $existing?->updated_at?->toDateTimeString(),
            categories: $data['category'] ?? [],
            tags: $data['tag'] ?? [],
            attributes: $normalizedAttributes,
        );
    }

    public static function fromArray(array $data): self
    {
        $specialPrice = $data['special_price'] ?? null;
        if (is_array($specialPrice)) {
            $specialPrice = $specialPrice['amount'] ?? null;
        }

        return new self(
            id: isset($data['id']) ? (int) $data['id'] : null,
            title: $data['title'] ?? null,
            slug: $data['slug'] ?? null,
            summary: $data['summary'] ?? null,
            description: $data['description'] ?? null,
            stock: isset($data['stock']) ? (int) $data['stock'] : null,
            status: $data['status'] ?? null,
            price: isset($data['price']) ? (float) $data['price'] : null,
            discount: isset($data['discount']) ? (float) $data['discount'] : null,
            is_featured: isset($data['is_featured']) ? (bool) $data['is_featured'] : null,
            d_deal: isset($data['d_deal']) ? (int) $data['d_deal'] : null,
            brand_id: isset($data['brand_id']) ? (int) $data['brand_id'] : null,
            attribute_set_id: isset($data['attribute_set_id']) ? (int) $data['attribute_set_id'] : null,
            sku: $data['sku'] ?? null,
            special_price: isset($specialPrice) ? (float) $specialPrice : null,
            special_price_start: $data['special_price_start'] ?? null,
            special_price_end: $data['special_price_end'] ?? null,
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null,
            categories: $data['category'] ?? [],
            tags: $data['tag'] ?? [],
            attributes: $data['attributes'] ?? [],
        );
    }
}
