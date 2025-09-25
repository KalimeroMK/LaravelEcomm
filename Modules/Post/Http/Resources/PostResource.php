<?php

declare(strict_types=1);

namespace Modules\Post\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Category\Http\Resources\CategoryResource;
use Modules\Post\Models\Post;

/** @mixin Post */
class PostResource extends JsonResource
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
            'images' => $this->whenLoaded('media', function () {
                return $this->getMedia('post')->map(function ($media): array {
                    return [
                        'id' => $media->id,
                        'url' => $media->getUrl(),
                        'name' => $media->name,
                        'mime_type' => $media->mime_type,
                    ];
                });
            }),
            'tags' => $this->tags,
            'post_cat_id' => $this->when(
                $this->relationLoaded('categories') && $this->categories->isNotEmpty(),
                fn () => $this->categories->first()->id ?? null,
                null
            ),
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'all_comments_count' => $this->when(
                $this->relationLoaded('comments'),
                fn () => $this->comments->count(),
                0
            ),
            'fpost_comments_count' => $this->when(
                $this->relationLoaded('comments'),
                fn () => $this->comments->where('type', 'fpost')->count(),
                0
            ),
            'post_comments_count' => $this->when(
                $this->relationLoaded('comments'),
                fn () => $this->comments->where('type', 'post')->count(),
                0
            ),
            'categories_count' => $this->when(
                $this->relationLoaded('categories'),
                fn () => $this->categories->count(),
                0
            ),
            'comments_count' => $this->when(
                $this->relationLoaded('comments'),
                fn () => $this->comments->count(),
                0
            ),
            'post_tag_count' => $this->when(
                $this->relationLoaded('tags'),
                fn () => $this->tags->count(),
                0
            ),
            'added_by' => $this->when(
                $this->relationLoaded('user'),
                fn () => $this->user->name ?? null,
                null
            ),
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
        ];
    }
}
