<?php

declare(strict_types=1);

namespace Modules\Page\Actions;

use Modules\Page\DTOs\PageDTO;
use Modules\Page\Models\Page;

class CreatePageAction
{
    public function execute(array $data): PageDTO
    {
        $page = Page::create($data);

        return new PageDTO($page);
    }
}
