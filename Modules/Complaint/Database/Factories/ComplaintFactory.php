<?php

namespace Modules\Complaint\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Modules\Complaint\Models\Complaint;
use Modules\Order\Models\Order;
use Modules\User\Models\User;

class ComplaintFactory extends Factory
{
    protected $model = Complaint::class;

    public function definition(): array
    {
        return [
            'status' => $this->faker->word(),
            'description' => $this->faker->text(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'user_id' => User::inRandomOrder()->first(),
            'order_id' => Order::inRandomOrder()->first(),
        ];
    }
}
