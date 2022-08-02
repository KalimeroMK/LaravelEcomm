<?php

namespace Modules\Post\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use JetBrains\PhpStorm\ArrayShape;
use Modules\Post\Models\Post;

class PostFactory extends Factory
{
    protected $model = Post::class;
    
    /**
     * Define the model's default state.
     *
     * @return array
     */
    #[ArrayShape([
        'title'       => "string",
        'summary'     => "string",
        'description' => "string",
        'quote'       => "string",
        'photo'       => "string",
        'slug'        => "string",
        'tags'        => "string",
        'added_by'    => "int",
        'created_at'  => "\Illuminate\Support\Carbon",
        'updated_at'  => "\Illuminate\Support\Carbon",
    ])] public function definition(): array
    {
        return [
            'title'       => $this->faker->word,
            'slug'        => $this->faker->slug,
            'summary'     => $this->faker->text,
            'description' => $this->faker->text,
            'quote'       => $this->faker->word,
            'photo'       => $this->faker->imageUrl(640, 480),
            'tags'        => $this->faker->word,
            'added_by'    => $this->faker->numberBetween(1, 3),
            'created_at'  => Carbon::now(),
            'updated_at'  => Carbon::now(),
        ];
    }
}
