<?php

declare(strict_types=1);

namespace Modules\Language\Actions;

use InvalidArgumentException;
use Modules\Language\DTOs\LanguageDTO;
use Modules\Language\Models\Language;

readonly class CreateLanguageAction
{
    public function execute(LanguageDTO $dto): Language
    {
        // If setting as default, unset other defaults first
        if ($dto->isDefault) {
            Language::where('is_default', true)->update(['is_default' => false]);
        }

        return Language::create($dto->toArray());
    }
}
