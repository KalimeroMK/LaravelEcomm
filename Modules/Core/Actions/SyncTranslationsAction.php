<?php

declare(strict_types=1);

namespace Modules\Core\Actions;

use InvalidArgumentException;
use Modules\Core\Traits\HasTranslations;
use Modules\Language\Models\Language;

/**
 * Action to sync/replace all translations for a model
 * 
 * Used by both Web and API controllers
 */
readonly class SyncTranslationsAction
{
    /**
     * Sync all translations for a model
     *
     * @param HasTranslations $model The model with translations
     * @param array<string, array<string, mixed>> $translations Locale => [field => value]
     * @return object The model instance
     */
    public function execute(
        object $model,
        array $translations
    ): object {
        $this->ensureTranslatable($model);

        // Filter out empty translations and validate locales
        $validTranslations = [];
        $activeLocales = Language::getActiveCodes();

        foreach ($translations as $locale => $fields) {
            // Skip invalid locales
            if (! in_array($locale, $activeLocales, true)) {
                continue;
            }

            // Filter out empty/null values
            $validFields = array_filter($fields, fn ($value) => ! blank($value));
            
            if (! empty($validFields)) {
                $validTranslations[$locale] = $validFields;
            }
        }

        return $model->syncTranslations($validTranslations);
    }

    /**
     * Delete translation for a specific locale
     *
     * @param HasTranslations $model The model with translations
     * @param string $locale The locale to delete
     * @return bool Success
     */
    public function deleteForLocale(object $model, string $locale): bool
    {
        $this->ensureTranslatable($model);

        return $model->deleteTranslation($locale);
    }

    /**
     * Delete all translations
     *
     * @param HasTranslations $model The model with translations
     * @return bool Success
     */
    public function deleteAll(object $model): bool
    {
        $this->ensureTranslatable($model);

        return $model->deleteAllTranslations();
    }

    /**
     * Copy translations from one locale to another
     *
     * @param HasTranslations $model The model with translations
     * @param string $sourceLocale Source locale
     * @param string $targetLocale Target locale
     * @return object The model instance
     */
    public function copy(
        object $model,
        string $sourceLocale,
        string $targetLocale
    ): object {
        $this->ensureTranslatable($model);

        $sourceTranslation = $model->translation($sourceLocale);
        
        if (! $sourceTranslation) {
            return $model;
        }

        $fields = $model->getTranslatableFields();
        $translations = [];

        foreach ($fields as $field) {
            $translations[$field] = $sourceTranslation->$field;
        }

        return $model->setTranslations($targetLocale, $translations);
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
