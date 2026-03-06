<?php

declare(strict_types=1);

namespace Modules\Core\Actions;

use InvalidArgumentException;
use Modules\Core\Traits\HasTranslations;
use Modules\Language\Models\Language;

/**
 * Action to get translations for a model
 * 
 * Used by both Web and API controllers
 */
readonly class GetTranslationAction
{
    /**
     * Get translation for a specific field
     *
     * @param HasTranslations $model The model with translations
     * @param string $field The field to translate
     * @param string|null $locale The locale (null = current locale)
     * @param bool $useFallback Whether to fallback to default language
     * @return mixed The translated value
     */
    public function getField(
        object $model,
        string $field,
        ?string $locale = null,
        bool $useFallback = true
    ): mixed {
        $this->ensureTranslatable($model);

        return $model->getTranslation($field, $locale, $useFallback);
    }

    /**
     * Get all translations for a specific locale
     *
     * @param HasTranslations $model The model with translations
     * @param string|null $locale The locale (null = current locale)
     * @param bool $useFallback Whether to fallback to default language
     * @return array<string, mixed>
     */
    public function getAllForLocale(
        object $model,
        ?string $locale = null,
        bool $useFallback = true
    ): array {
        $this->ensureTranslatable($model);

        $locale = $locale ?? app()->getLocale();
        $fields = $model->getTranslatableFields();
        $result = [];

        foreach ($fields as $field) {
            $result[$field] = $model->getTranslation($field, $locale, $useFallback);
        }

        return $result;
    }

    /**
     * Get all translations for all locales
     *
     * @param HasTranslations $model The model with translations
     * @param array<string>|null $locales Specific locales to get (null = all active)
     * @return array<string, array<string, mixed>>
     */
    public function getAllForModel(
        object $model,
        ?array $locales = null
    ): array {
        $this->ensureTranslatable($model);

        $locales = $locales ?? Language::getActiveCodes();
        $result = [];

        foreach ($locales as $locale) {
            $result[$locale] = $this->getAllForLocale($model, $locale, false);
        }

        return $result;
    }

    /**
     * Get model with translated attributes for current locale
     *
     * @param HasTranslations $model The model with translations
     * @return array<string, mixed>
     */
    public function toArray(object $model): array
    {
        $this->ensureTranslatable($model);

        return $model->toArrayWithTranslations();
    }

    /**
     * Ensure the model uses HasTranslations trait
     *
     * @throws InvalidArgumentException
     */
    private function ensureTranslatable(object $model): void
    {
        if (! in_array(HasTranslations::class, class_uses_recursive($model), true)) {
            throw new InvalidArgumentException(
                'Model must use HasTranslations trait: ' . get_class($model)
            );
        }
    }
}
