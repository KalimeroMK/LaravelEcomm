<?php

namespace Modules\Notification\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Modules\Notification\Models\Notification;
use Modules\User\Models\User;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        return [
            'type' => $this->faker->word(),
            'notifiable_id' => User::role('super-admin')->first()->id ?? 3,
            'notifiable_type' => $this->faker->word(),
            'data' => $this->faker->word(),
            'read_at' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
