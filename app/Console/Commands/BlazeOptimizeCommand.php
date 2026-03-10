<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Front\Services\Theme\ThemeManager;
use Symfony\Component\Console\Helper\Table;

/**
 * Blaze Optimization Command
 *
 * Manage Blaze caching and optimization for themes.
 */
class BlazeOptimizeCommand extends Command
{
    protected $signature = 'blaze:optimize
                            {--theme= : Specific theme to optimize}
                            {--all : Optimize all themes}
                            {--clear : Clear cache instead of warming}
                            {--status : Show current status}
                            {--recommendations : Show optimization recommendations}';

    protected $description = 'Manage Blaze optimization for themes';

    private ThemeManager $themeManager;

    public function __construct(ThemeManager $themeManager)
    {
        parent::__construct();
        $this->themeManager = $themeManager;
    }

    public function handle(): int
    {
        if ($this->option('status')) {
            return $this->showStatus();
        }

        if ($this->option('recommendations')) {
            return $this->showRecommendations();
        }

        if ($this->option('clear')) {
            return $this->clearCache();
        }

        if ($this->option('all')) {
            return $this->optimizeAllThemes();
        }

        $theme = $this->option('theme') ?? $this->themeManager->getActiveTheme();
        return $this->optimizeTheme($theme);
    }

    private function showStatus(): int
    {
        $status = $this->themeManager->getBlazeStatus();
        $themes = $this->themeManager->getAvailableThemes();

        $this->info('=== Blaze Status ===');
        $this->newLine();

        $this->table(
            ['Setting', 'Value'],
            [
                ['Enabled', $status['enabled'] ? '<fg=green>Yes</>' : '<fg=red>No</>'],
                ['Debug Mode', $status['debug'] ? '<fg=yellow>Yes</>' : 'No'],
                ['Active Theme', $status['active_theme']],
                ['Theme Blaze Enabled', $status['theme_enabled'] ? '<fg=green>Yes</>' : '<fg=red>No</>'],
                ['View::share() Support', $status['view_share_support'] ? '<fg=green>Yes</>' : 'No'],
                ['View::composer() Support', $status['view_composer_support'] ? '<fg=green>Yes</>' : 'No'],
                ['Cache Warming', $status['cache_warming_enabled'] ? '<fg=green>Enabled</>' : '<fg=gray>Disabled</>'],
            ]
        );

        $this->newLine();
        $this->info('Available Themes:');
        
        $themeRows = [];
        foreach ($themes as $theme) {
            $isActive = $theme['name'] === $status['active_theme'];
            $themeRows[] = [
                $theme['name'] . ($isActive ? ' <fg=green>(active)</>' : ''),
                $theme['enabled'] ? '<fg=green>Enabled</>' : '<fg=red>Disabled</>',
                $theme['blaze_enabled'] ? '<fg=green>Yes</>' : '<fg=red>No</>',
                $this->formatStrategy($theme['strategy']),
            ];
        }

        $this->table(['Theme', 'Enabled', 'Blaze', 'Strategy'], $themeRows);

        return self::SUCCESS;
    }

    private function showRecommendations(): int
    {
        $recommendations = $this->themeManager->getOptimizationRecommendations();

        if (empty($recommendations)) {
            $this->info('No specific recommendations. Your theme is well optimized!');
            return self::SUCCESS;
        }

        $this->warn('Optimization Recommendations:');
        $this->newLine();

        foreach ($recommendations as $rec) {
            $typeColor = match ($rec['type']) {
                'memo' => 'blue',
                'fold' => 'yellow',
                default => 'white',
            };

            $this->line("  <fg={$typeColor}>[{$rec['type']}]</> {$rec['message']}");
            
            if (isset($rec['config_path'])) {
                $this->line("  → Config: <fg=cyan>{$rec['config_path']}</>");
            }
            
            $this->newLine();
        }

        return self::SUCCESS;
    }

    private function clearCache(): int
    {
        $theme = $this->option('theme');

        if ($this->option('all')) {
            $this->info('Clearing Blaze cache for all themes...');
            $results = $this->themeManager->warmAllThemes();
            
            foreach ($results as $themeName => $result) {
                $icon = $result['success'] ? '<fg=green>✓</>' : '<fg=red>✗</>';
                $this->line("{$icon} {$themeName}: {$result['message']}");
            }
        } else {
            $theme = $theme ?? $this->themeManager->getActiveTheme();
            $this->info("Clearing Blaze cache for theme: {$theme}...");
            
            $result = $this->themeManager->clearBlazeCache($theme);
            
            if ($result['success']) {
                $this->info("<fg=green>✓</> {$result['message']}");
            } else {
                $this->error("<fg=red>✗</> {$result['message']}");
                return self::FAILURE;
            }
        }

        return self::SUCCESS;
    }

    private function optimizeAllThemes(): int
    {
        $this->info('Optimizing all themes...');
        $this->newLine();

        $results = $this->themeManager->warmAllThemes();
        $totalCompiled = 0;
        $totalFailed = 0;

        foreach ($results as $themeName => $result) {
            $icon = $result['success'] ? '<fg=green>✓</>' : '<fg=red>✗</>';
            
            if ($result['success']) {
                $this->line("{$icon} {$themeName}: <fg=green>{$result['compiled']}</> compiled, <fg=red>{$result['failed']}</> failed");
                $totalCompiled += $result['compiled'];
                $totalFailed += $result['failed'];
            } else {
                $this->line("{$icon} {$themeName}: {$result['message']}");
            }

            if (! empty($result['errors'])) {
                foreach ($result['errors'] as $error) {
                    $this->line("  <fg=red>→</> {$error['file']}: {$error['error']}");
                }
            }
        }

        $this->newLine();
        $this->info("Total: <fg=green>{$totalCompiled}</> compiled, <fg=red>{$totalFailed}</> failed");

        return $totalFailed > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function optimizeTheme(string $theme): int
    {
        if (! $this->themeManager->themeExists($theme)) {
            $this->error("Theme '{$theme}' does not exist.");
            return self::FAILURE;
        }

        $this->info("Optimizing theme: {$theme}...");
        $this->newLine();

        $result = $this->themeManager->prewarmBlazeCache($theme);

        if (! $result['success']) {
            $this->error($result['message']);
            return self::FAILURE;
        }

        $progress = $this->output->createProgressBar($result['compiled'] + $result['failed']);
        $progress->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s%');
        $progress->start();

        // Simulate progress (actual compilation already happened)
        for ($i = 0; $i < $result['compiled'] + $result['failed']; $i++) {
            $progress->advance();
            usleep(1000);
        }
        $progress->finish();
        $this->newLine(2);

        $this->info("<fg=green>✓</> Compiled: {$result['compiled']}");
        
        if ($result['failed'] > 0) {
            $this->warn("<fg=red>✗</> Failed: {$result['failed']}");
            
            if (! empty($result['errors'])) {
                $this->newLine();
                $this->warn('Errors:');
                foreach ($result['errors'] as $error) {
                    $this->line("  <fg=red>→</> {$error['file']}");
                }
            }
        }

        return $result['failed'] > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function formatStrategy(array $strategy): string
    {
        $parts = [];
        
        if ($strategy['compile'] ?? false) {
            $parts[] = 'compile';
        }
        if ($strategy['memo'] ?? false) {
            $parts[] = 'memo';
        }
        if ($strategy['fold'] ?? false) {
            $parts[] = '<fg=yellow>fold</>';
        }

        return empty($parts) ? '<fg=gray>none</>' : implode('+', $parts);
    }
}
