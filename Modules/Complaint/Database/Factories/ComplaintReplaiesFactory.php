<?php

namespace Modules\Complaint\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Modules\Complaint\Models\Complaint;
use Modules\Complaint\Models\ComplaintReply;
use Modules\User\Models\User;

class ComplaintReplaiesFactory extends Factory
{
    protected $model = ComplaintReply::class;

    public function definition(): array
    {
        return [
            'reply_content' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'complaint_id' => Complaint::factory(),
            'user_id' => User::factory(),
        ];
    }
}
