<?php

declare(strict_types=1);

namespace Modules\Message\Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Message\Models\Message;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    /**
     * @return array<string>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'subject' => $this->faker->word,
            'email' => $this->faker->unique()->safeEmail,
            'photo' => $this->faker->word,
            'phone' => $this->faker->phoneNumber,
            'message' => $this->faker->word,
            'read_at' => Carbon::now(),
        ];
    }
}
