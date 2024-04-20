<?php

namespace Modules\Banner\Http\Resource;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Banner\Models\Banner;

/** @mixin Banner */
class BannerResource extends JsonResource
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string> Array of field rules.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'photo' => $this->photo,
            'description' => $this->description,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
