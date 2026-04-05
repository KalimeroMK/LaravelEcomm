<?php

declare(strict_types=1);

namespace Modules\Page\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Modules\Core\Database\Factories\Traits\HasTranslationsFactory;
use Modules\Page\Models\Page;
use Modules\User\Models\User;

class PageFactory extends Factory
{
    use HasTranslationsFactory;

    protected $model = Page::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->word(),
            'slug' => $this->faker->slug(),
            'content' => $this->faker->word(),
            'is_active' => $this->faker->boolean(),
            'user_id' => User::factory(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    /**
     * Configure the factory to create page with translations.
     * Creates translations for all configured locales.
     */
    public function withTranslations(?array $locales = null): self
    {
        return $this->afterCreating(function (Model $model) use ($locales): void {
            /** @var Page $page */
            $page = $model;
            
            $localesToUse = $locales ?? $this->translationLocales;
            
            foreach ($localesToUse as $locale) {
                $localeSuffix = $locale === 'en' ? '' : ' (' . strtoupper($locale) . ')';
                
                $page->translations()->create([
                    'locale' => $locale,
                    'title' => $this->faker->words(3, true) . $localeSuffix,
                    'slug' => $this->faker->slug() . ($locale === 'en' ? '' : '-' . $locale),
                    'description' => $this->faker->paragraphs(4, true) . $localeSuffix,
                    'meta_title' => $this->faker->words(5, true) . $localeSuffix,
                    'meta_description' => $this->faker->sentence(12) . $localeSuffix,
                    'meta_keywords' => $this->faker->words(5, true) . $localeSuffix,
                ]);
            }
        });
    }

    /**
     * Configure the factory to create page with specific translation data.
     *
     * @param array<string, array<string, mixed>> $translations Locale => fields mapping
     */
    public function withTranslationData(array $translations): self
    {
        return $this->afterCreating(function (Model $model) use ($translations): void {
            /** @var Page $page */
            $page = $model;
            
            foreach ($translations as $locale => $fields) {
                $page->translations()->create([
                    'locale' => $locale,
                    ...$fields,
                ]);
            }
        });
    }
}
