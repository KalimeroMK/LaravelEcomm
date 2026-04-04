<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\Post\Repository\PostRepository;

class BlogSearchAction
{
    public function __construct(private readonly PostRepository $postRepository) {}

    public function __invoke(Request $request): array
    {
        $term  = $request->input('search', '');
        $posts = $this->postRepository->searchByTerm($term, 10);

        $recentPosts = Cache::remember('recent_posts_sidebar', 3600, fn () => $this->postRepository->getRecent(3));

        return [
            'posts'       => $posts,
            'recantPosts' => $recentPosts,
        ];
    }
}
