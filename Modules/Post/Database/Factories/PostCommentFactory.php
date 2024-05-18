<?php

namespace Modules\Post\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Modules\Post\Models\PostComment;

class PostCommentFactory extends Factory
{
    protected $model = PostComment::class;

    /**
     * @return array|mixed[]
     */
    public function definition(): array
    {
        return [
            'user_id' => $this->faker->numberBetween(1, 3),
            'comment' => $this->faker->word,
            'replied_comment' => $this->faker->word,
            'parent_id' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'post_id' => $this->faker->numberBetween(1, 100),
        ];
    }
}
