<?php

declare(strict_types=1);

namespace Modules\Page\Actions;

use Modules\Page\DTOs\PageListDTO;
use Modules\Page\Models\Page;

class GetAllPagesAction
{
    public function execute(): PageListDTO
    {
        $pages = Page::all();

        return new PageListDTO($pages);
    }
}
