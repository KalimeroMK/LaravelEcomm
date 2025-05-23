<?php

declare(strict_types=1);

namespace Modules\Newsletter\Actions;

use Modules\Newsletter\Models\Newsletter;

class DeleteNewsletterAction
{
    public function execute(int $id): void
    {
        Newsletter::destroy($id);
    }
}
