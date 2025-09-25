<?php

declare(strict_types=1);

namespace Modules\Newsletter\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Newsletter\Models\Newsletter;

class NewsletterFactory extends Factory
{
    protected $model = Newsletter::class;

    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->safeEmail(),
            'is_validated' => 1,
        ];
    }
}
