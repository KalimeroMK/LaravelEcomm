<?php

namespace Modules\Bundle\Http\Resource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Modules\Bundle\Models\Bundle;
use Modules\Product\Http\Resources\ProductResource;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property float $price
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int $products_count
 * @mixin Bundle
 */
class BundleResource extends JsonResource
{
    /**
     * Transforms the resource into an array.
     *
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
            'products_count' => $this->whenLoaded('products_count'),
            'products' => ProductResource::collection($this->whenLoaded('products')),
            'images' => $this->whenLoaded('media', function () {
                return $this->getMedia('bundle')->map(function ($media) {
                    return [
                        'id' => $media->id,
                        'url' => $media->getUrl(),
                        'name' => $media->name,
                        'size' => $media->size,
                        'mime_type' => $media->mime_type,
                    ];
                });
            }),
        ];
    }
}
