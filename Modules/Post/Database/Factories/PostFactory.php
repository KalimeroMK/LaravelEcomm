<?php

namespace Modules\Post\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
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
            'slug' => $this->faker->slug(),
            'summary' => $this->faker->text(),
            'description' => $this->faker->text(),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'user_id' => User::inRandomOrder()->first()->id,
        ];
    }

    /**
     * Configure the factory to create a post with random existing categories.
     */
    public function withCategories(): PostFactory
    {
        return $this->afterCreating(function (Model $post) {
            $post = $post->fresh(); // Ensure the model instance is fresh
            /** @var Post $post */
            $categories = Category::inRandomOrder()->limit(3)->pluck('id');
            $post->categories()->attach($categories);
        });
    }

    /**
     * Configure the factory to create a post with random existing tags.
     */
    public function withTags(): PostFactory
    {
        return $this->afterCreating(function (Model $post) {
            $post = $post->fresh(); // Ensure the model instance is fresh
            /** @var Post $post */
            $tags = Tag::inRandomOrder()->limit(5)->pluck('id');
            $post->tags()->attach($tags);
        });
    }

    /**
     * Configure the factory to create a post with random existing categories and tags.
     */
    public function withCategoriesAndTags(): PostFactory
    {
        return $this->afterCreating(function (Model $post) {
            $post = $post->fresh(); // Ensure the model instance is fresh
            /** @var Post $post */
            $categories = Category::inRandomOrder()->limit(3)->pluck('id');
            $post->categories()->attach($categories);

            $tags = Tag::inRandomOrder()->limit(5)->pluck('id');
            $post->tags()->attach($tags);
        });
    }
}
