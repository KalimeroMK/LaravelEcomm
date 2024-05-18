<?php

namespace Modules\Post\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Modules\Category\Models\CategoryPost;

class PostCategoryFactory extends Factory
{
    protected $model = CategoryPost::class;

    /**
     * @return array|mixed[]
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->unique()->title,
            'slug' => $this->faker->slug,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
