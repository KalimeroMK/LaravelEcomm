<?php

declare(strict_types=1);

namespace Modules\Post\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Modules\Category\Models\Category;
use Modules\Post\Models\Post;
use Modules\Tag\Models\Tag;
use Modules\User\Models\User;

class PostFactory extends Factory
{
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
            'user_id' => User::factory()->create()->id,
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
}
