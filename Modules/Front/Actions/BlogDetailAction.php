<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Illuminate\Support\Facades\Cache;
use Modules\Post\Repository\PostRepository;
use Modules\Tag\Repository\TagRepository;

class BlogDetailAction
{
    public function __construct(
        private readonly PostRepository $postRepository,
        private readonly TagRepository $tagRepository,
    ) {}

    public function __invoke(string $slug): array
    {
        $post = $this->postRepository->findBySlug($slug);

        $recentPosts = Cache::remember('recent_posts_sidebar', 3600, fn () => $this->postRepository->getRecent(3));

        $tags = Cache::remember('blog_tag_cloud', 3600, fn () => $this->tagRepository->getWithPosts(50));

        return [
            'post'        => $post,
            'recantPosts' => $recentPosts,
            'tags'        => $tags,
        ];
    }
}
