<?php

declare(strict_types=1);

namespace Modules\Core\Database\Factories\Traits;

use Illuminate\Database\Eloquent\Model;

/**
 * Trait for factories to create translations for models.
 *
 * @example
 * class ProductFactory extends Factory
 * {
 *     use HasTranslationsFactory;
 *
 *     public function definition(): array
 *     {
 *         return [
 *             'title' => $this->faker->word(),
 *             // ...
 *         ];
 *     }
 *
 *     public function configure(): static
 *     {
 *         return $this->afterCreating(function (Model $model) {
 *             $this->createTranslations($model, [
 *                 'name' => $this->faker->word(),
 *                 'summary' => $this->faker->text(),
 *                 'description' => $this->faker->text(),
 *             ]);
 *         });
 *     }
 * }
 */
trait HasTranslationsFactory
{
    /**
     * Available locales for translations.
     *
     * @var array<string>
     */
    protected array $translationLocales = ['en', 'mk', 'de'];

    /**
     * Set custom locales for translations.
     *
     * @param array<string> $locales
     */
    public function withLocales(array $locales): self
    {
        $this->translationLocales = $locales;

        return $this;
    }

    /**
     * Create translations for a model.
     *
     * @param Model $model The model instance
     * @param array<string, mixed> $translatableFields Fields to translate (key => value or key => callable)
     * @param string|null $relationName Name of the translations relation (default: 'translations')
     */
    public function createTranslations(
        Model $model,
        array $translatableFields,
        ?string $relationName = null
    ): void {
        $relationName = $relationName ?? 'translations';
        $translations = [];

        foreach ($this->translationLocales as $locale) {
            $translationData = ['locale' => $locale];

            foreach ($translatableFields as $field => $value) {
                // If value is callable, generate new value for each locale
                if (is_callable($value)) {
                    $translationData[$field] = $value($locale);
                } else {
                    // For non-callable values, add locale suffix to make them unique
                    $translationData[$field] = $locale === 'en' 
                        ? $value 
                        : $value . ' (' . strtoupper($locale) . ')';
                }
            }

            $translations[] = $translationData;
        }

        // Create translations
        if (method_exists($model, $relationName)) {
            foreach ($translations as $translationData) {
                $model->{$relationName}()->create($translationData);
            }
        }
    }

    /**
     * Create translations with faker-generated content.
     *
     * @param Model $model The model instance
     * @param array<string> $fieldNames Field names to generate translations for
     * @param array<string, callable>|null $customGenerators Custom generators per field
     * @param string|null $relationName Name of the translations relation
     */
    public function createTranslationsWithFaker(
        Model $model,
        array $fieldNames,
        ?array $customGenerators = null,
        ?string $relationName = null
    ): void {
        $relationName = $relationName ?? 'translations';
        $customGenerators = $customGenerators ?? [];

        foreach ($this->translationLocales as $locale) {
            $translationData = ['locale' => $locale];

            foreach ($fieldNames as $field) {
                if (isset($customGenerators[$field])) {
                    $translationData[$field] = $customGenerators[$field]($this->faker, $locale);
                } else {
                    // Default generators based on field name
                    $translationData[$field] = $this->generateDefaultTranslation($field, $locale);
                }
            }

            if (method_exists($model, $relationName)) {
                $model->{$relationName}()->create($translationData);
            }
        }
    }

    /**
     * Generate default translation value based on field name.
     */
    protected function generateDefaultTranslation(string $field, string $locale): mixed
    {
        $localeSuffix = $locale === 'en' ? '' : ' (' . strtoupper($locale) . ')';

        return match (true) {
            str_contains($field, 'title') || str_contains($field, 'name') => $this->faker->words(3, true) . $localeSuffix,
            str_contains($field, 'summary') => $this->faker->sentence(10) . $localeSuffix,
            str_contains($field, 'description') => $this->faker->paragraphs(3, true) . $localeSuffix,
            str_contains($field, 'slug') => $this->faker->slug() . ($locale === 'en' ? '' : '-' . $locale),
            str_contains($field, 'meta_title') => $this->faker->words(5, true) . $localeSuffix,
            str_contains($field, 'meta_description') => $this->faker->sentence(15) . $localeSuffix,
            str_contains($field, 'content') => $this->faker->paragraphs(5, true) . $localeSuffix,
            default => $this->faker->word() . $localeSuffix,
        };
    }

    /**
     * Configure factory to create translations after model creation.
     *
     * @param array<string> $fieldNames Field names to translate
     */
    public function withTranslations(array $fieldNames = ['name', 'summary', 'description']): self
    {
        return $this->afterCreating(function (Model $model) use ($fieldNames): void {
            $this->createTranslationsWithFaker($model, $fieldNames);
        });
    }

    /**
     * Configure factory to create translations for specific locales only.
     *
     * @param array<string> $locales Locales to create translations for
     * @param array<string> $fieldNames Field names to translate
     */
    public function withTranslationsForLocales(array $locales, array $fieldNames = ['name', 'summary', 'description']): self
    {
        return $this->afterCreating(function (Model $model) use ($locales, $fieldNames): void {
            $originalLocales = $this->translationLocales;
            $this->translationLocales = $locales;
            $this->createTranslationsWithFaker($model, $fieldNames);
            $this->translationLocales = $originalLocales;
        });
    }
}
