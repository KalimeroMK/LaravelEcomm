<?php

declare(strict_types=1);

namespace Modules\Front\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Banner\Models\Banner;
use Modules\Brand\Models\Brand;
use Modules\Category\Models\Category;
use Modules\Front\Services\FrontCacheService;
use Modules\Post\Models\Post;
use Modules\Product\Models\Product;

class OptimizeFrontPerformance extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'front:optimize {--force : Force optimization without confirmation}';

    /**
     * The console command description.
     */
    protected $description = 'Optimize front-end performance by clearing caches and warming up frequently accessed data';

    /**
     * Execute the console command.
     */
    public function handle(FrontCacheService $cacheService): int
    {
        if (! $this->option('force') && ! $this->confirm('Do you want to proceed with front-end performance optimization?')) {
            $this->info('Front-end performance optimization cancelled.');

            return Command::SUCCESS;
        }

        $this->info('🚀 Starting front-end performance optimization...');

        try {
            // Step 1: Clear all front-end caches
            $this->info('📦 Clearing front-end caches...');
            $clearedKeys = $cacheService->clearAll();
            $this->info("  ✅ Cleared {$clearedKeys} cache keys");

            // Step 2: Warm up frequently accessed data
            $this->info('🔥 Warming up frequently accessed data...');
            $this->warmUpData($cacheService);

            // Step 3: Check cache statistics
            $this->info('📊 Checking cache performance...');
            $this->checkCachePerformance($cacheService);

            $this->info('✅ Front-end performance optimization completed successfully!');

            return Command::SUCCESS;
        } catch (Exception $e) {
            $this->error('❌ Front-end performance optimization failed: '.$e->getMessage());

            return Command::FAILURE;
        }
    }

    /**
     * Warm up frequently accessed data
     */
    private function warmUpData(FrontCacheService $cacheService): void
    {
        // Warm up featured products
        $this->line('  - Warming up featured products...');
        $cacheService->rememberLong('featured_products_warmup', function () {
            return Product::with(['categories'])
                ->where('status', 'active')
                ->where('is_featured', true)
                ->orderBy('price', 'desc')
                ->limit(4)
                ->get();
        });

        // Warm up latest products
        $this->line('  - Warming up latest products...');
        $cacheService->rememberShort('latest_products_warmup', function () {
            return Product::with(['categories'])
                ->where('status', 'active')
                ->orderBy('id', 'desc')
                ->limit(4)
                ->get();
        });

        // Warm up active categories
        $this->line('  - Warming up active categories...');
        $cacheService->rememberLong('active_categories_warmup', function () {
            return Category::where('status', 'active')
                ->orderBy('title')
                ->get();
        });

        // Warm up active brands
        $this->line('  - Warming up active brands...');
        $cacheService->rememberLong('active_brands_warmup', function () {
            return Brand::where('status', 'active')
                ->withCount('products')
                ->orderBy('title')
                ->get();
        });

        // Warm up recent posts
        $this->line('  - Warming up recent posts...');
        $cacheService->rememberLong('recent_posts_warmup', function () {
            return Post::where('status', 'active')
                ->orderBy('id', 'desc')
                ->limit(3)
                ->get();
        });

        // Warm up active banners
        $this->line('  - Warming up active banners...');
        $cacheService->rememberLong('active_banners_warmup', function () {
            return Banner::with('categories')
                ->get()
                ->filter(fn ($b): bool => $b->isActive());
        });

        $this->info('  ✅ Data warm-up completed');
    }

    /**
     * Check cache performance
     */
    private function checkCachePerformance(FrontCacheService $cacheService): void
    {
        try {
            $stats = $cacheService->getStats();

            $this->line("    ✓ Cache hit rate: {$stats['hit_rate']}%");
            $this->line("    ✓ Memory usage: {$stats['used_memory']}");
            $this->line("    ✓ Connected clients: {$stats['connected_clients']}");
            $this->line("    ✓ Total keys: {$stats['total_keys']}");

            if (isset($stats['error'])) {
                $this->warn("    ⚠️ Error: {$stats['error']}");
            }
        } catch (Exception $e) {
            $this->warn('    ⚠️ Could not retrieve cache statistics: '.$e->getMessage());
        }
    }
}
