<?php

declare(strict_types=1);

namespace Modules\Product\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductDownload;

/** @extends Factory<ProductDownload> */
class ProductDownloadFactory extends Factory
{
    protected $model = ProductDownload::class;

    public function definition(): array
    {
        $fileName = $this->faker->word() . '.pdf';
        
        return [
            'product_id' => Product::factory()->create(['type' => Product::TYPE_DOWNLOADABLE]),
            'file_name' => $fileName,
            'file_path' => 'downloads/' . $this->faker->uuid() . '/' . $fileName,
            'original_name' => $fileName,
            'mime_type' => $this->faker->randomElement([
                'application/pdf',
                'application/zip',
                'image/jpeg',
                'video/mp4',
            ]),
            'file_size' => $this->faker->numberBetween(1024, 10485760), // 1KB to 10MB
            'sort_order' => $this->faker->numberBetween(0, 10),
            'is_active' => true,
        ];
    }

    /**
     * Set the download as inactive.
     */
    public function inactive(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
            ];
        });
    }

    /**
     * Set a specific file size.
     */
    public function fileSize(int $bytes): self
    {
        return $this->state(function (array $attributes) use ($bytes) {
            return [
                'file_size' => $bytes,
            ];
        });
    }
}
