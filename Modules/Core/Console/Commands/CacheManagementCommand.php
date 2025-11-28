<?php

declare(strict_types=1);

namespace Modules\Core\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Services\CacheService;

class CacheManagementCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'cache:manage 
                            {action : Action to perform (stats|clear|invalidate)}
                            {--model= : Model to invalidate cache for}
                            {--pattern= : Pattern to match for invalidation}';

    /**
     * The console command description.
     */
    protected $description = 'Manage application cache';

    public function __construct(private CacheService $cacheService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $action = $this->argument('action');

        return match ($action) {
            'stats' => $this->showStats(),
            'clear' => $this->clearCache(),
            'invalidate' => $this->invalidateCache(),
            default => $this->showHelp(),
        };
    }

    /**
     * Show cache statistics
     */
    private function showStats(): int
    {
        $stats = $this->cacheService->getStats();

        $this->info('Cache Statistics:');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Memory Used', $stats['memory_used']],
                ['Memory Peak', $stats['memory_peak']],
                ['Hit Rate', $stats['hit_rate'].'%'],
                ['Total Keys', $stats['total_keys']],
            ]
        );

        return Command::SUCCESS;
    }

    /**
     * Clear all cache
     */
    private function clearCache(): int
    {
        if ($this->confirm('Are you sure you want to clear all cache?')) {
            $this->cacheService->clearAll();
            $this->info('All cache cleared successfully!');
        } else {
            $this->info('Cache clear cancelled.');
        }

        return Command::SUCCESS;
    }

    /**
     * Invalidate specific cache
     */
    private function invalidateCache(): int
    {
        $model = $this->option('model');
        $pattern = $this->option('pattern');

        if ($model) {
            $this->cacheService->invalidateModelCache($model);
            $this->info("Cache invalidated for model: {$model}");
        } elseif ($pattern) {
            $this->cacheService->invalidateApiCache($pattern);
            $this->info("Cache invalidated for pattern: {$pattern}");
        } else {
            $this->error('Please specify --model or --pattern option');

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Show help information
     */
    private function showHelp(): int
    {
        $this->info('Available actions:');
        $this->line('  stats       - Show cache statistics');
        $this->line('  clear       - Clear all cache');
        $this->line('  invalidate  - Invalidate specific cache');
        $this->line('');
        $this->info('Examples:');
        $this->line('  php artisan cache:manage stats');
        $this->line('  php artisan cache:manage clear');
        $this->line('  php artisan cache:manage invalidate --model=Product');
        $this->line('  php artisan cache:manage invalidate --pattern=products');

        return Command::SUCCESS;
    }
}
