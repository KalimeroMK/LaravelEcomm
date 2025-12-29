<?php

declare(strict_types=1);

namespace Modules\Page\DTOs;

use Illuminate\Support\Collection;

class PageListDTO
{
    /**
     * @param Collection<int, \Modules\Page\Models\Page> $pages
     */
    public function __construct(public Collection $pages) {}
}
