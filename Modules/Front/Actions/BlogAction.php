<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Illuminate\Support\Facades\Cache;
use Modules\Post\Repository\PostRepository;

class BlogAction
{
    public function __construct(private readonly PostRepository $postRepository) {}

    public function __invoke(): array
    {
        $posts      = $this->postRepository->getActivePaginated(9);
        $recentPosts = Cache::remember('recent_posts_sidebar', 3600, fn () => $this->postRepository->getRecent(3));

        return [
            'posts'       => $posts,
            'recantPosts' => $recentPosts,
        ];
    }
}
