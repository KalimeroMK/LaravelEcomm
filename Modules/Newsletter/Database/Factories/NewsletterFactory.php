<?php

namespace Modules\Newsletter\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Modules\Newsletter\Models\Newsletter;

class NewsletterFactory extends Factory
{
    protected $model = Newsletter::class;
    
    public function definition(): array
    {
        return [
            'email'        => $this->faker->unique()->safeEmail(),
            'is_validated' => true,
            'created_at'   => Carbon::now(),
            'updated_at'   => Carbon::now(),
        ];
    }
}
