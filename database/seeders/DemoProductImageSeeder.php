<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Bundle\Models\Bundle;
use Modules\Product\Models\Product;
use Throwable;

/**
 * Attaches placeholder photos to every product and bundle that has no media.
 *
 * DatabaseSeeder only attaches images to the products it creates itself;
 * products from AnalyticsDemoDataSeeder, and all bundles from
 * BundleDatabaseSeeder, end up imageless and render as generated
 * placeholders on the storefront.
 * Safe to re-run: it only touches records without media.
 */
class DemoProductImageSeeder extends Seeder
{
    private const IMAGES_PER_PRODUCT = 2;

    public function run(): void
    {
        $this->backfill(Product::doesntHave('media')->get(), 'product', self::IMAGES_PER_PRODUCT);
        $this->backfill(Bundle::doesntHave('media')->get(), 'bundle', 1);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Collection<int, Model>  $records
     */
    private function backfill($records, string $collection, int $imagesPerRecord): void
    {
        $done = 0;
        $skipped = 0;

        foreach ($records as $record) {
            $attached = 0;

            for ($i = 0; $i < $imagesPerRecord; $i++) {
                $url = 'https://picsum.photos/800/800?random='.random_int(1, 100000);
                $contents = @file_get_contents($url);

                if ($contents === false) {
                    continue;
                }

                $tmp = tempnam(sys_get_temp_dir(), $collection.'_image');
                file_put_contents($tmp, $contents);

                try {
                    $record->addMedia($tmp)->toMediaCollection($collection);
                    $attached++;
                } catch (Throwable $e) {
                    $this->command?->warn(ucfirst($collection)." {$record->id}: {$e->getMessage()}");
                    @unlink($tmp);
                }
            }

            $attached > 0 ? $done++ : $skipped++;

            if (($done + $skipped) % 50 === 0) {
                $this->command?->info($collection.': '.($done + $skipped).'/'.$records->count());
            }
        }

        $this->command?->info("Images attached to {$done} {$collection}s, {$skipped} left without (download failures).");
    }
}
