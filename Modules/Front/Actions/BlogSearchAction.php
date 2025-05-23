<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Modules\Post\Models\Post;

class BlogSearchAction
{
    public function __invoke($request): array
    {
        $searchTerm = $request->input('search', '');
        $posts = Post::with('author')
            ->where('title', 'like', '%'.$searchTerm.'%')
            ->paginate(10);
        $recantPosts = Post::whereStatus('active')->orderBy('id', 'DESC')->limit(3)->get();

        return [
            'posts' => $posts,
            'recantPosts' => $recantPosts,
        ];
    }
}
