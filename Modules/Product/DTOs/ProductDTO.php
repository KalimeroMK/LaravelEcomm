<?php

declare(strict_types=1);

namespace Modules\Product\DTOs;

use Modules\Product\Models\Product;

class ProductDTO
{
    public int $id;

    public string $title;

    public string $description;

    public ?string $summary;

    public ?string $photo;

    public string $status;

    public ?array $categories;

    public ?array $tags;

    public ?array $brand;

    public ?array $attributes;

    public ?array $author;

    public string $created_at;

    public function __construct(Product $product)
    {
        $this->id = $product->id;
        $this->title = $product->title;
        $this->description = $product->description;
        $this->summary = $product->summary;
        $this->photo = $product->photo;
        $this->status = $product->status;
        $this->categories = $product->categories ? $product->categories->toArray() : [];
        $this->tags = $product->tags ? $product->tags->toArray() : [];
        $this->brand = $product->brand ? $product->brand->toArray() : null;
        $this->attributes = $product->attributes ? $product->attributes->toArray() : [];
        $this->author = $product->author ? $product->author->toArray() : null;
        $this->created_at = $product->created_at->toDateTimeString();
    }
}
