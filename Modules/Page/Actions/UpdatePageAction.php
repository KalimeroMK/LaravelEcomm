<?php

declare(strict_types=1);

namespace Modules\Page\Actions;

use Modules\Page\DTOs\PageDTO;
use Modules\Page\Models\Page;

class UpdatePageAction
{
    public function execute(int $id, array $data): PageDTO
    {
        $page = Page::findOrFail($id);
        $page->update($data);

        return new PageDTO($page);
    }
}
