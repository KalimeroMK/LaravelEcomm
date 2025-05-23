<?php

declare(strict_types=1);

namespace Modules\Page\DTOs;

class PageListDTO
{
    public array $pages;

    public function __construct($pages)
    {
        $this->pages = $pages->toArray();
    }
}
