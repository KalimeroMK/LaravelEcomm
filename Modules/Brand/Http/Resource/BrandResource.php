<?php

namespace Modules\Brand\Http\Resource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Brand\Models\Brand;

/** @mixin Brand */
class BrandResource extends JsonResource
{

    /**
     * @return string[]
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'images' => $this->getMedia('brand')->map(function ($media) {
                return $media->getUrl();
            }),
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
