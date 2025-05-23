<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Modules\Post\Models\Post;

class BlogByCategoryAction
{
    public function __invoke(string $slug): array
    {
        $posts = Post::with('author')->whereHas('categories', static function ($q) use ($slug) {
            $q->whereSlug($slug);
        })->paginate(10);
        $recantPosts = Post::whereStatus('active')->orderBy('id', 'DESC')->limit(3)->get();

        return [
            'posts' => $posts,
            'recantPosts' => $recantPosts,
        ];
    }
}
