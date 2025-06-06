<?php

declare(strict_types=1);

namespace Modules\Product\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Brand\Http\Resource\BrandResource;
use Modules\Category\Http\Resources\CategoryResource;
use Modules\Product\Models\Product;

/** @mixin Product */
class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'summary' => $this->summary,
            'description' => $this->description,
            'stock' => $this->stock,
            'status' => $this->status,
            'price' => $this->price,
            'discount' => $this->discount,
            'is_featured' => $this->is_featured,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'd_deal' => $this->d_deal,
            'special_price' => $this->special_price,
            'special_price_start' => $this->special_price_start,
            'special_price_end' => $this->special_price_end,
            'brand' => new BrandResource($this->whenLoaded('brand')),
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
        ];
    }
}
