<?php

declare(strict_types=1);

namespace Modules\Message\Actions;

use Modules\Message\Models\Message;

class MarkMultipleAsReadAction
{
    public function execute(array $messageIds): int
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized - Admin access required');
        }
        
        return Message::whereIn('id', $messageIds)->update(['is_read' => true]);
    }
}
