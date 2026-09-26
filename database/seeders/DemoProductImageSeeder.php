<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Product\Models\Product;
use Throwable;

/**
 * Attaches placeholder photos to every product that has no media.
 *
 * DatabaseSeeder only attaches images to the products it creates itself;
 * products from AnalyticsDemoDataSeeder (and any other source) end up
 * imageless and render as generated placeholders on the storefront.
 * Safe to re-run: it only touches products without media.
 */
class DemoProductImageSeeder extends Seeder
{
    private const IMAGES_PER_PRODUCT = 2;

    public function run(): void
    {
        $products = Product::doesntHave('media')->get();
        $done = 0;
        $skipped = 0;

        foreach ($products as $product) {
            $attached = 0;

            for ($i = 0; $i < self::IMAGES_PER_PRODUCT; $i++) {
                $url = 'https://picsum.photos/800/800?random='.random_int(1, 100000);
                $contents = @file_get_contents($url);

                if ($contents === false) {
                    continue;
                }

                $tmp = tempnam(sys_get_temp_dir(), 'product_image');
                file_put_contents($tmp, $contents);

                try {
                    $product->addMedia($tmp)->toMediaCollection('product');
                    $attached++;
                } catch (Throwable $e) {
                    $this->command?->warn("Product {$product->id}: {$e->getMessage()}");
                    @unlink($tmp);
                }
            }

            $attached > 0 ? $done++ : $skipped++;

            if (($done + $skipped) % 50 === 0) {
                $this->command?->info(($done + $skipped).'/'.$products->count());
            }
        }

        $this->command?->info("Images attached to {$done} products, {$skipped} left without (download failures).");
    }
}
