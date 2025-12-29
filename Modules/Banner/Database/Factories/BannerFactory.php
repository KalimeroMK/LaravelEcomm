<?php

declare(strict_types=1);

namespace Modules\Banner\Database\Factories;

use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Modules\Banner\Models\Banner;

class BannerFactory extends Factory
{
    protected $model = Banner::class;

    /**
     * @return array<string, string>
     */
    public function definition(): array
    {
        return [
            'title' => 'title'.mb_strtoupper(Str::random(10)),
            'slug' => 'slug'.mb_strtoupper(Str::random(10)),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'description' => $this->faker->text,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    public function withMedia(): self
    {
        return $this->afterCreating(function (Model $model): void {
            /** @var Banner $banner */
            $banner = $model;
            $count = 1; // Default to 1 image, or make this configurable if needed
            for ($i = 0; $i < $count; $i++) {
                try {
                    $banner->addMediaFromUrl('https://picsum.photos/1920/1080')
                        ->toMediaCollection('banner');
                } catch (Exception $e) {
                    // Fail silently or log if needed, but don't break seeding
                }
            }
        });
    }
}
