<?php

declare(strict_types=1);

namespace Modules\Bundle\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Modules\Bundle\Models\Bundle;

class BundleFactory extends Factory
{
    protected $model = Bundle::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'slug' => $this->faker->slug(),
            'description' => $this->faker->text(),
            'price' => $this->faker->randomFloat(2),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    public function withMedia(int $count = 2): self
    {
        return $this->afterCreating(function (Model $model) use ($count): void {
            /** @var Bundle $bundle */
            $bundle = $model;
            for ($i = 0; $i < $count; $i++) {
                $imageUrl = 'https://picsum.photos/800/600?random='.rand(1, 10000);
                $imageContents = @file_get_contents($imageUrl);
                if ($imageContents !== false) {
                    $tempFile = tempnam(sys_get_temp_dir(), 'bundle_image');
                    file_put_contents($tempFile, $imageContents);
                    $bundle->addMedia($tempFile)->preservingOriginal()->toMediaCollection('bundle');
                    @unlink($tempFile);
                }
            }
        });
    }
}
