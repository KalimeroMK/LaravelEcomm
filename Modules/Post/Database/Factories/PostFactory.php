<?php

declare(strict_types=1);

namespace Modules\Post\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Modules\Category\Models\Category;
use Modules\Core\Database\Factories\Traits\HasTranslationsFactory;
use Modules\Post\Models\Post;
use Modules\Tag\Models\Tag;
use Modules\User\Models\User;

class PostFactory extends Factory
{
    use HasTranslationsFactory;

    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->word(),
            'slug' => 'slug-'.mb_strtoupper(Str::random(10)),
            'summary' => $this->faker->text(),
            'description' => $this->faker->text(),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'user_id' => User::first()?->id ?? 1,
        ];
    }

    /**
     * Configure the factory to create a post with random existing categories.
     */
    public function withCategories(): self
    {
        return $this->afterCreating(function (Model $post): void {
            $post = $post->fresh(); // Ensure the model instance is fresh
            /** @var Post $post */
            $categories = Category::inRandomOrder()->limit(3)->pluck('id');
            $post->categories()->attach($categories);
        });
    }

    /**
     * Configure the factory to create a post with random existing tags.
     */
    public function withTags(): self
    {
        return $this->afterCreating(function (Model $post): void {
            $post = $post->fresh(); // Ensure the model instance is fresh
            /** @var Post $post */
            $tags = Tag::inRandomOrder()->limit(5)->pluck('id');
            $post->tags()->attach($tags);
        });
    }

    /**
     * Configure the factory to create a post with random existing categories and tags.
     */
    public function withCategoriesAndTags(): self
    {
        return $this->afterCreating(function (Model $post): void {
            $post = $post->fresh(); // Ensure the model instance is fresh
            /** @var Post $post */
            $categories = Category::inRandomOrder()->limit(3)->pluck('id');
            $post->categories()->attach($categories);

            $tags = Tag::inRandomOrder()->limit(5)->pluck('id');
            $post->tags()->attach($tags);
        });
    }

    /**
     * Configure the factory to create a post with media image.
     */
    public function withMedia(): self
    {
        return $this->afterCreating(function (Model $model): void {
            /** @var Post $post */
            $post = $model;

            $imageUrl = 'https://picsum.photos/1200/800?random='.rand(1, 10000);
            $imageContents = @file_get_contents($imageUrl);

            if ($imageContents !== false) {
                $tempFile = tempnam(sys_get_temp_dir(), 'post_image');
                file_put_contents($tempFile, $imageContents);

                $post->addMedia($tempFile)
                    ->preservingOriginal()
                    ->toMediaCollection('post');

                @unlink($tempFile);
            }
        });
    }

    /**
     * Configure the factory to create post with translations.
     * Creates translations for all configured locales.
     */
    public function withTranslations(?array $locales = null): self
    {
        return $this->afterCreating(function (Model $model) use ($locales): void {
            /** @var Post $post */
            $post = $model;
            
            $localesToUse = $locales ?? $this->translationLocales;
            
            foreach ($localesToUse as $locale) {
                $localeSuffix = $locale === 'en' ? '' : ' (' . strtoupper($locale) . ')';
                
                $post->translations()->create([
                    'locale' => $locale,
                    'title' => $this->faker->words(4, true) . $localeSuffix,
                    'slug' => $this->faker->slug() . ($locale === 'en' ? '' : '-' . $locale),
                    'summary' => $this->faker->sentence(15) . $localeSuffix,
                    'content' => $this->faker->paragraphs(5, true) . $localeSuffix,
                    'meta_title' => $this->faker->words(6, true) . $localeSuffix,
                    'meta_description' => $this->faker->sentence(12) . $localeSuffix,
                    'meta_keywords' => $this->faker->words(5, true) . $localeSuffix,
                ]);
            }
        });
    }

    /**
     * Configure the factory to create post with specific translation data.
     *
     * @param array<string, array<string, mixed>> $translations Locale => fields mapping
     */
    public function withTranslationData(array $translations): self
    {
        return $this->afterCreating(function (Model $model) use ($translations): void {
            /** @var Post $post */
            $post = $model;
            
            foreach ($translations as $locale => $fields) {
                $post->translations()->create([
                    'locale' => $locale,
                    ...$fields,
                ]);
            }
        });
    }
}
