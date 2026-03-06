<?php

declare(strict_types=1);

namespace Modules\Core\Actions;

use InvalidArgumentException;
use Modules\Core\Traits\HasTranslations;

/**
 * Action to set translations for a model
 * 
 * Used by both Web and API controllers
 */
readonly class SetTranslationAction
{
    /**
     * Set a single translation field
     *
     * @param HasTranslations $model The model with translations
     * @param string $field The field to translate
     * @param string $locale The locale code
     * @param mixed $value The translated value
     * @return object The model instance
     */
    public function setField(
        object $model,
        string $field,
        string $locale,
        mixed $value
    ): object {
        $this->ensureTranslatable($model);

        return $model->setTranslation($field, $locale, $value);
    }

    /**
     * Set multiple translations for a locale
     *
     * @param HasTranslations $model The model with translations
     * @param string $locale The locale code
     * @param array<string, mixed> $translations Field => value pairs
     * @return object The model instance
     */
    public function setForLocale(
        object $model,
        string $locale,
        array $translations
    ): object {
        $this->ensureTranslatable($model);

        return $model->setTranslations($locale, $translations);
    }

    /**
     * Set translations for multiple locales at once
     *
     * @param HasTranslations $model The model with translations
     * @param array<string, array<string, mixed>> $translationsByLocale Locale => [field => value]
     * @return object The model instance
     */
    public function setMultiple(
        object $model,
        array $translationsByLocale
    ): object {
        $this->ensureTranslatable($model);

        foreach ($translationsByLocale as $locale => $translations) {
            $this->setForLocale($model, $locale, $translations);
        }

        return $model;
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
