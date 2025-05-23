<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Illuminate\Support\Facades\Cache;
use Modules\Post\Models\Post;

class BlogAction
{
    public function __invoke(): array
    {
        return Cache::remember('blog', 24 * 60, function (): array {
            return [
                'posts' => Post::with(['author'])->whereStatus('active')->orderBy('id', 'DESC')->paginate(9),
                'recantPosts' => Post::with(['author'])->whereStatus('active')->orderBy('id', 'DESC')->limit(3)->get(),
            ];
        });
    }
}
