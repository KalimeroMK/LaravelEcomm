<?php

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
     * @param  Request  $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'summary' => $this->summary,
            'description' => $this->description,
            'stock' => $this->stock,
            'size' => $this->size,
            'condition' => $this->condition,
            'status' => $this->status,
            'price' => $this->price,
            'discount' => $this->discount,
            'is_featured' => $this->is_featured,
            'created_at' => $this->created_at,
            'carts_count' => $this->carts_count,
            'product_reviews_count' => $this->product_reviews_count,
            'image_url' => $this->image_url,
            'color' => $this->color,
            'd_deal' => $this->d_deal,
            'get_review_count' => $this->get_review_count,
            'sizes_count' => $this->sizes_count,
            'special_price' => $this->special_price,
            'special_price_start' => $this->special_price_start,
            'special_price_end' => $this->special_price_end,
            'brand' => new BrandResource($this->whenLoaded('brand')),
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
        ];
    }
}
