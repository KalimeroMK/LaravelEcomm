<?php

declare(strict_types=1);

namespace Modules\Message\Actions;

use Modules\Message\Models\Message;

class MarkAsReadAction
{
    public function execute(Message $message): void
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized - Admin access required');
        }
        
        $message->update(['is_read' => true]);
    }
}
