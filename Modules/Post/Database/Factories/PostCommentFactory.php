<?php

namespace Modules\Post\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use JetBrains\PhpStorm\ArrayShape;
use Modules\Post\Models\PostComment;

class PostCommentFactory extends Factory
{
    protected $model = PostComment::class;
    
    /**
     * Define the model's default state.
     *
     * @return array
     */
    #[ArrayShape([
        'user_id'         => "int",
        'comment'         => "string",
        'replied_comment' => "string",
        'parent_id'       => "int",
        'created_at'      => "\Illuminate\Support\Carbon",
        'updated_at'      => "\Illuminate\Support\Carbon",
        'post_id'         => "int",
    ])] public function definition(): array
    {
        return [
            'user_id'         => $this->faker->numberBetween(1, 3),
            'comment'         => $this->faker->word,
            'replied_comment' => $this->faker->word,
            'parent_id'       => $this->faker->randomNumber(),
            'created_at'      => Carbon::now(),
            'updated_at'      => Carbon::now(),
            'post_id'         => $this->faker->numberBetween(1, 100),
        ];
    }
}
