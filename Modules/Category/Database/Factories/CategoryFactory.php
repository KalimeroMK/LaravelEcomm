<?php

declare(strict_types=1);

namespace Modules\Category\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Modules\Category\Models\Category;
use Modules\Core\Database\Factories\Traits\HasTranslationsFactory;

class CategoryFactory extends Factory
{
    use HasTranslationsFactory;

    protected $model = Category::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->unique()->words(2, true);
        
        return [
            'title' => ucwords($title),
            'slug' => Str::slug($title) . '-' . Str::random(5),
            'status' => $this->faker->randomElement(['active', 'active', 'active', 'inactive']), // 75% active
            'parent_id' => null, // Top level by default
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    /**
     * Create a subcategory
     */
    public function subcategory(?Category $parent = null): self
    {
        return $this->state(function (array $attributes) use ($parent) {
            return [
                'parent_id' => $parent?->id,
            ];
        });
    }

    /**
     * Active category
     */
    public function active(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'active',
            ];
        });
    }

    /**
     * Inactive category
     */
    public function inactive(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'inactive',
            ];
        });
    }

    /**
     * Configure the factory to create category with translations.
     * Creates translations for all configured locales.
     */
    public function withTranslations(?array $locales = null): self
    {
        return $this->afterCreating(function (Model $model) use ($locales): void {
            /** @var Category $category */
            $category = $model;
            
            $localesToUse = $locales ?? $this->translationLocales;
            
            foreach ($localesToUse as $locale) {
                $localeSuffix = $locale === 'en' ? '' : ' (' . strtoupper($locale) . ')';
                
                $category->translations()->create([
                    'locale' => $locale,
                    'title' => $this->faker->words(2, true) . $localeSuffix,
                    'slug' => $this->faker->slug() . ($locale === 'en' ? '' : '-' . $locale),
                    'summary' => $this->faker->sentence(10) . $localeSuffix,
                    'description' => $this->faker->paragraphs(2, true) . $localeSuffix,
                    'meta_title' => $this->faker->words(5, true) . $localeSuffix,
                    'meta_description' => $this->faker->sentence(12) . $localeSuffix,
                ]);
            }
        });
    }

    /**
     * Configure the factory to create category with specific translation data.
     *
     * @param array<string, array<string, mixed>> $translations Locale => fields mapping
     */
    public function withTranslationData(array $translations): self
    {
        return $this->afterCreating(function (Model $model) use ($translations): void {
            /** @var Category $category */
            $category = $model;
            
            foreach ($translations as $locale => $fields) {
                $category->translations()->create([
                    'locale' => $locale,
                    ...$fields,
                ]);
            }
        });
    }
}
