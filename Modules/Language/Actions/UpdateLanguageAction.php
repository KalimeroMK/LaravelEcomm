<?php

declare(strict_types=1);

namespace Modules\Language\Actions;

use InvalidArgumentException;
use Modules\Language\DTOs\LanguageDTO;
use Modules\Language\Models\Language;

readonly class UpdateLanguageAction
{
    public function execute(Language $language, LanguageDTO $dto): Language
    {
        // Prevent deactivating default language
        if (! $dto->isActive && $language->is_default) {
            throw new InvalidArgumentException('Cannot deactivate the default language.');
        }

        // If setting as default, unset other defaults
        if ($dto->isDefault && ! $language->is_default) {
            Language::where('is_default', true)->update(['is_default' => false]);
        }

        $language->update($dto->toArray());

        return $language->fresh();
    }
}
