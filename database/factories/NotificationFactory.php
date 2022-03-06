<?php

    namespace Database\Factories;

    use Illuminate\Database\Eloquent\Factories\Factory;
    use Illuminate\Support\Carbon;
    use JetBrains\PhpStorm\ArrayShape;
    use Modules\Notification\Models\Notification;

    class NotificationFactory extends Factory
    {
        protected $model = Notification::class;

        /**
         * Define the model's default state.
         *
         * @return array
         */
        #[ArrayShape([
            'type'            => "string",
            'notifiable_type' => "string",
            'notifiable_id'   => "int",
            'data'            => "string",
            'read_at'         => "string",
            'created_at'      => "\Illuminate\Support\Carbon",
            'updated_at'      => "\Illuminate\Support\Carbon",
        ])] public function definition(): array
        {
            return [
                'type'            => $this->faker->word,
                'notifiable_type' => $this->faker->word,
                'notifiable_id'   => $this->faker->randomNumber(),
                'data'            => $this->faker->word,
                'read_at'         => $this->faker->word,
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now(),
            ];
        }
    }
