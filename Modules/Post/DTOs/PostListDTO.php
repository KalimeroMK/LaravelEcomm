<?php

declare(strict_types=1);

namespace Modules\Post\DTOs;

class PostListDTO
{
    public array $posts;

    public function __construct($posts)
    {
        $this->posts = $posts->toArray();
    }
}
