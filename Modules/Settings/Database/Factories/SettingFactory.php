<?php

namespace Modules\Settings\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Modules\Settings\Models\Setting;

class SettingFactory extends Factory
{
    protected $model = Setting::class;

    public function definition(): array
    {
        return [
            'description' => $this->faker->text(),
            'short_des' => $this->faker->word(),
            'logo' => $this->faker->imageUrl(),
            'address' => $this->faker->address(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'site-name' => $this->faker->word(),
            'keywords' => $this->faker->word(),
            'google-site-verification' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
