<?php

declare(strict_types=1);

namespace Modules\Message\Actions;

use Modules\Message\Models\Message;

class DeleteMessageAction
{
    public function execute(int $id): void
    {
        Message::destroy($id);
    }
}
