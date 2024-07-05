<?php

namespace Modules\Post\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Modules\Post\Models\Post;
use Modules\Post\Models\PostComment;
use Modules\User\Models\User;

class PostCommentFactory extends Factory
{
    protected $model = PostComment::class;

    /**
     * @return array|mixed[]
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'comment' => $this->faker->word,
            'replied_comment' => $this->faker->word,
            'parent_id' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'post_id' => Post::inRandomOrder()->first()->id,
        ];
    }
}
