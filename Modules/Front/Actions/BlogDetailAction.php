<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Illuminate\Support\Facades\Cache;
use Modules\Post\Models\Post;
use Modules\Tag\Models\Tag;

class BlogDetailAction
{
    public function __invoke(string $slug): array|string
    {
        $cacheKey = 'blogDetail_'.$slug;

        return Cache::remember($cacheKey, 24 * 60, function () use ($slug) {
            $post = Post::with(['author', 'categories'])->whereSlug($slug)->firstOrFail();
            $recentPosts = Post::with('author')->whereStatus('active')->orderBy('id', 'DESC')->limit(3)->get();
            $tags = Tag::whereHas('posts')->take(50)->get();

            return [
                'post' => $post,
                'recantPosts' => $recentPosts,
                'tags' => $tags,
            ];
        });
    }
}
