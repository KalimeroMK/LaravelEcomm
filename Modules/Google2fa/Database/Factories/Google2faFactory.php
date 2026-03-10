<?php

declare(strict_types=1);

namespace Modules\Google2fa\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Google2fa\Models\Google2fa;
use Modules\User\Models\User;

class Google2faFactory extends Factory
{
    protected $model = Google2fa::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'google2fa_enable' => $this->faker->boolean(),
            'google2fa_secret' => $this->faker->bothify('################'),
            'recovery_codes' => $this->faker->optional()->randomElements([
                mb_strtoupper($this->faker->bothify('########-########')),
                mb_strtoupper($this->faker->bothify('########-########')),
                mb_strtoupper($this->faker->bothify('########-########')),
            ], 3),
        ];
    }
}
