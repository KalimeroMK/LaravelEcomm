<?php

namespace Modules\Post\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Category\Http\Resources\CategoryResource;
use Modules\Post\Models\Post;

/** @mixin Post */
class PostResource extends JsonResource
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
            'quote' => $this->quote,
            'photo' => $this->photo,
            'tags' => $this->tags,
            'post_cat_id' => $this->post_cat_id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'all_comments_count' => $this->all_comments_count,
            'fpost_comments_count' => $this->fpost_comments_count,
            'post_comments_count' => $this->post_comments_count,
            'categories_count' => $this->categories_count,
            'comments_count' => $this->comments_count,
            'image_url' => $this->image_url,
            'post_tag_count' => $this->post_tag_count,
            'added_by' => $this->added_by,
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
        ];
    }
}
