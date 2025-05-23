<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Illuminate\Support\Facades\Cache;
use Modules\Post\Models\Post;

class BlogByTagAction
{
    public function __invoke(string $slug): array
    {
        $cacheKey = 'blogByTag_'.$slug;

        return Cache::remember($cacheKey, 24 * 60, function () use ($slug) {
            $posts = Post::with(['author', 'tags'])
                ->whereHas('tags', function ($q) use ($slug) {
                    $q->where('slug', $slug);
                })
                ->paginate(10);
            $recent_posts = Post::with('author')->where('status', 'active')->orderBy('id', 'DESC')->limit(3)->get();

            return [
                'posts' => $posts,
                'recent_posts' => $recent_posts,
            ];
        });
    }
}
