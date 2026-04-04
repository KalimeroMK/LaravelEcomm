<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Illuminate\Support\Facades\Cache;
use Modules\Post\Repository\PostRepository;

class BlogByTagAction
{
    public function __construct(private readonly PostRepository $postRepository) {}

    public function __invoke(string $slug): array
    {
        $posts        = $this->postRepository->getByTag($slug, 10);
        $recent_posts = Cache::remember('recent_posts_sidebar', 3600, fn () => $this->postRepository->getRecent(3));

        return [
            'posts'        => $posts,
            'recent_posts' => $recent_posts,
        ];
    }
}
