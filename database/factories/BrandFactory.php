<?php

    namespace Database\Factories;

    use Illuminate\Database\Eloquent\Factories\Factory;
    use Illuminate\Support\Carbon;
    use JetBrains\PhpStorm\ArrayShape;
    use Modules\Brand\Models\Brand;

    class BrandFactory extends Factory
    {
        protected $model = Brand::class;

        /**
         * Define the model's default state.
         *
         * @return array
         */
        #[ArrayShape([
            'title'      => "string",
            'created_at' => "\Illuminate\Support\Carbon",
            'updated_at' => "\Illuminate\Support\Carbon",
        ])] public function definition(): array
        {
            return [
                'title'      => $this->faker->unique(true)->title,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
    }
