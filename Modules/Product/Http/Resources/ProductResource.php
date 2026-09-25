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
            'type' => $this->type,
            'title' => $this->title,
            'slug' => $this->slug,
            'summary' => $this->summary,
            'description' => $this->description,
            'stock' => $this->stock,
            'status' => $this->status,
            'price' => $this->price,
            'discount' => $this->discount,
            'is_featured' => $this->is_featured,
            'is_virtual' => $this->is_virtual,
            'is_downloadable' => $this->is_downloadable,
            'requires_shipping' => $this->requiresShipping(),
            'service_starts_at' => $this->service_starts_at,
            'service_ends_at' => $this->service_ends_at,
            'service_duration_minutes' => $this->service_duration_minutes,
            'max_downloads' => $this->max_downloads,
            'download_expiry_days' => $this->download_expiry_days,
            'downloads' => $this->when($this->isDownloadable() && $this->relationLoaded('downloads'), function () {
                return $this->activeDownloads->map(function ($download) {
                    return [
                        'id' => $download->id,
                        'file_name' => $download->file_name,
                        'file_size' => $download->formatted_file_size,
                    ];
                });
            }),
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
