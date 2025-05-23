<?php

declare(strict_types=1);

namespace Modules\Page\DTOs;

use Modules\Page\Models\Page;

class PageDTO
{
    public int $id;

    public string $title;

    public string $content;

    public string $created_at;

    public function __construct(Page $page)
    {
        $this->id = $page->id;
        $this->title = $page->title;
        $this->content = $page->content;
        $this->created_at = $page->created_at->toDateTimeString();
    }
}
