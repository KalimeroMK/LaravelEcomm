<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use JetBrains\PhpStorm\ArrayShape;
use Modules\Settings\Models\Setting;

class SettingFactory extends Factory
{
    protected $model = Setting::class;
    
    /**
     * Define the model's default state.
     *
     * @return array
     */
    #[ArrayShape([
        'description' => "string",
        'short_des'   => "string",
        'logo'        => "string",
        'photo'       => "string",
        'address'     => "string",
        'phone'       => "string",
        'email'       => "string",
        'created_at'  => "\Illuminate\Support\Carbon",
        'updated_at'  => "\Illuminate\Support\Carbon",
    ])] public function definition(): array
    {
        return [
            'description' => $this->faker->text,
            'short_des'   => $this->faker->word,
            'logo'        => $this->faker->word,
            'photo'       => $this->faker->word,
            'address'     => $this->faker->address,
            'phone'       => $this->faker->phoneNumber,
            'email'       => $this->faker->unique()->safeEmail,
            'created_at'  => Carbon::now(),
            'updated_at'  => Carbon::now(),
        ];
    }
}
