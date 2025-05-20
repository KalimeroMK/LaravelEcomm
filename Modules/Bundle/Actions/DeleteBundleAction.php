<?php

declare(strict_types=1);

namespace Modules\Bundle\Actions;

use Modules\Bundle\Models\Bundle;

readonly class DeleteBundleAction
{
    public function execute(int $id): bool
    {
        $bundle = Bundle::findOrFail($id);
        // Optionally detach products or delete related files here
        return (bool)$bundle->delete();
    }
}
