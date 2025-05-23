<?php

declare(strict_types=1);

namespace Modules\Page\Actions;

use Modules\Page\Models\Page;

class DeletePageAction
{
    public function execute(int $id): void
    {
        Page::destroy($id);
    }
}
