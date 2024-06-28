<?php

namespace Modules\Post\Models\Observers;

use Illuminate\Support\Str;
use Modules\Post\Models\Post;

class PostObserver
{
    /**
     * Handle the post "created" event.
     *
     * @param  Post  $post
     */
    public function creating(Post $post): void
    {
        $slug = Str::slug($post->title);
        if (Post::whereSlug($slug)->count() > 0) {
            $post->slug = $slug;
        }
        $post->slug = $post->incrementSlug($slug);
    }

}
