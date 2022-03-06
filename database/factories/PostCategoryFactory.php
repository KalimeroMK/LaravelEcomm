<?php

    namespace Database\Factories;

    use Illuminate\Database\Eloquent\Factories\Factory;
    use Illuminate\Support\Carbon;
    use JetBrains\PhpStorm\ArrayShape;
    use Modules\Post\Models\PostCategory;

    class PostCategoryFactory extends Factory
    {
        protected $model = PostCategory::class;

        /**
         * Define the model's default state.
         *
         * @return array
         */
        #[ArrayShape([
            'title'      => "string",
            'slug'       => "string",
            'created_at' => "\Illuminate\Support\Carbon",
            'updated_at' => "\Illuminate\Support\Carbon",
        ])] public function definition(): array
        {
            return [
                'title'      => $this->faker->unique()->title,
                'slug'       => $this->faker->slug,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
    }
