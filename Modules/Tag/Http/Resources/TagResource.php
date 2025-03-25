<?php

declare(strict_types=1);

namespace Modules\Tag\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Post\Http\Resources\PostResource;
use Modules\Tag\Models\Tag;

/** @mixin Tag */
class TagResource extends JsonResource
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
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'posts_count' => $this->posts_count,

            'posts' => PostResource::collection($this->whenLoaded('posts')),
        ];
    }
}
