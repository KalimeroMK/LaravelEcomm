<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Modules\Post\Models\Post;

class BlogFilterAction
{
    public function __invoke(array $data): array
    {
        $query = Post::with('author')->whereStatus('active');
        if (! empty($data['search'])) {
            $query->where('title', 'like', '%'.$data['search'].'%');
        }
        if (! empty($data['category'])) {
            $query->whereHas('categories', function ($q) use ($data) {
                $q->where('slug', $data['category']);
            });
        }
        $posts = $query->paginate(10);
        $recantPosts = Post::whereStatus('active')->orderBy('id', 'DESC')->limit(3)->get();

        return [
            'posts' => $posts,
            'recantPosts' => $recantPosts,
        ];
    }
}
