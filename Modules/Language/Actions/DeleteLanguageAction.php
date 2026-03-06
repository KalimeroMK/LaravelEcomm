<?php

declare(strict_types=1);

namespace Modules\Language\Actions;

use InvalidArgumentException;
use Modules\Language\Models\Language;

readonly class DeleteLanguageAction
{
    public function execute(Language $language): void
    {
        if ($language->is_default) {
            throw new InvalidArgumentException('Cannot delete the default language.');
        }

        $language->delete();
    }
}
