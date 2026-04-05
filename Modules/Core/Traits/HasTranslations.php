<?php

declare(strict_types=1);

namespace Modules\Core\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\App;
use Modules\Language\Models\Language;

trait HasTranslations
{
    /** @var string|null */
    protected ?string $translationLocale = null;

    /**
     * Get the translation relationship name
     */
    abstract public function getTranslationRelation(): string;

    /**
     * Get the translatable fields
     *
     * @return array<string>
     */
    abstract public function getTranslatableFields(): array;

    /**
     * Get translations relation
     */
    public function translations(): HasMany
    {
        return $this->hasMany($this->getTranslationModelClass(), $this->getTranslationForeignKey());
    }

    /**
     * Get translation model class
     */
    protected function getTranslationModelClass(): string
    {
        return $this->getTranslationRelation();
    }

    /**
     * Get the foreign key for this model
     */
    protected function getTranslationForeignKey(): string
    {
        return strtolower(class_basename(static::class)) . '_id';
    }

    /**
     * Get translation for specific locale
     */
    public function translation(?string $locale = null): ?object
    {
        $locale = $locale ?? $this->getLocale();

        return $this->translations->first(function ($translation) use ($locale) {
            return $translation->locale === $locale;
        });
    }

    /**
     * Set the locale for translations
     */
    public function setLocale(string $locale): static
    {
        $this->translationLocale = $locale;

        return $this;
    }

    /**
     * Get current locale
     */
    public function getLocale(): string
    {
        return $this->translationLocale ?? App::getLocale();
    }

    /**
     * Check if has translation for locale
     */
    public function hasTranslation(string $locale): bool
    {
        return $this->translations->contains('locale', $locale);
    }

    /**
     * Set translation for a field
     */
    public function setTranslation(string $field, string $locale, mixed $value): static
    {
        $this->guardAgainstInvalidField($field);

        $translation = $this->translations()->firstOrNew([
            'locale' => $locale,
        ]);

        $translation->$field = $value;
        $translation->save();

        // Refresh relationship
        $this->load('translations');

        return $this;
    }

    /**
     * Set multiple translations at once
     *
     * @param array<string, mixed> $translations
     */
    public function setTranslations(string $locale, array $translations): static
    {
        foreach ($translations as $field => $value) {
            $this->setTranslation($field, $locale, $value);
        }

        return $this;
    }

    /**
     * Get translation for a field
     */
    public function getTranslation(string $field, ?string $locale = null, bool $useFallback = true): mixed
    {
        $this->guardAgainstInvalidField($field);

        $locale = $locale ?? $this->getLocale();
        $translation = $this->translation($locale);

        if ($translation && ! blank($translation->$field)) {
            return $translation->$field;
        }

        if ($useFallback) {
            return $this->getFallbackTranslation($field, $locale);
        }

        return null;
    }

    /**
     * Get fallback translation
     */
    protected function getFallbackTranslation(string $field, string $locale): mixed
    {
        // Try default language
        $defaultLocale = Language::getDefaultCode();
        if ($locale !== $defaultLocale) {
            $defaultTranslation = $this->translation($defaultLocale);
            if ($defaultTranslation && ! blank($defaultTranslation->$field)) {
                return $defaultTranslation->$field;
            }
        }

        // Return original field value from model
        return $this->getAttribute($field);
    }

    /**
     * Delete translation for a locale
     */
    public function deleteTranslation(string $locale): bool
    {
        return (bool) $this->translations()->where('locale', $locale)->delete();
    }

    /**
     * Delete all translations
     */
    public function deleteAllTranslations(): bool
    {
        return (bool) $this->translations()->delete();
    }

    /**
     * Sync translations - replace all translations with new ones
     *
     * @param array<string, array<string, mixed>> $translations
     */
    public function syncTranslations(array $translations): static
    {
        // Delete existing translations
        $this->deleteAllTranslations();

        // Create new translations
        foreach ($translations as $locale => $fields) {
            $validFields = array_intersect_key($fields, array_flip($this->getTranslatableFields()));
            if (! empty($validFields)) {
                $this->translations()->create([
                    'locale' => $locale,
                    ...$validFields,
                ]);
            }
        }

        // Refresh relationship
        $this->load('translations');

        return $this;
    }

    /**
     * Get all translations for all locales
     *
     * @return array<string, array<string, mixed>>
     */
    public function getAllTranslations(): array
    {
        $result = [];

        foreach ($this->translations as $translation) {
            $result[$translation->locale] = $translation->only($this->getTranslatableFields());
        }

        return $result;
    }

    /**
     * Guard against invalid field
     *
     * @throws \InvalidArgumentException
     */
    protected function guardAgainstInvalidField(string $field): void
    {
        if (! in_array($field, $this->getTranslatableFields(), true)) {
            throw new \InvalidArgumentException(
                "Field '{$field}' is not translatable on " . static::class
            );
        }
    }

    /**
     * Get translated attribute explicitly
     */
    public function getTranslatedAttribute(string $key, ?string $locale = null): mixed
    {
        if (in_array($key, $this->getTranslatableFields(), true)) {
            return $this->getTranslation($key, $locale);
        }
        return $this->getAttribute($key);
    }

    /**
     * Set translated attribute explicitly
     */
    public function setTranslatedAttribute(string $key, $value, ?string $locale = null): static
    {
        if (in_array($key, $this->getTranslatableFields(), true)) {
            $this->setTranslation($key, $locale ?? $this->getLocale(), $value);
            return $this;
        }
        return parent::setAttribute($key, $value);
    }

    /**
     * Convert model to array with translations
     *
     * @return array<string, mixed>
     */
    public function toArrayWithTranslations(?array $locales = null): array
    {
        $attributes = $this->toArray();
        $locales = $locales ?? Language::getActiveCodes();

        foreach ($locales as $locale) {
            $translation = $this->translation($locale);
            if ($translation) {
                $attributes['translations'][$locale] = $translation->only($this->getTranslatableFields());
            }
        }

        return $attributes;
    }

    /**
     * Scope to load translations with eager loading
     */
    public function scopeWithTranslations($query, ?string $locale = null): void
    {
        if ($locale) {
            $query->with(['translations' => function ($q) use ($locale): void {
                $q->where('locale', $locale);
            }]);
        } else {
            $query->with('translations');
        }
    }
}
