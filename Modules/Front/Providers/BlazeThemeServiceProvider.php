<?php

declare(strict_types=1);

namespace Modules\Front\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Blaze\Blaze;
use Modules\Front\Services\Theme\ThemeManager;

/**
 * Blaze Theme Service Provider
 *
 * Configures Blaze optimization for multi-theme system.
 * Automatically discovers and optimizes all theme directories.
 */
class BlazeThemeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            base_path('config/blaze.php'),
            'blaze'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if (! config('blaze.enabled', true)) {
            return;
        }

        $this->configureBlaze();
        $this->configureDebugMode();
    }

    /**
     * Configure Blaze for all themes.
     */
    private function configureBlaze(): void
    {
        $themeManager = app(ThemeManager::class);
        $activeTheme = $themeManager->getActiveTheme();
        
        // Configure shared components first
        $this->configureSharedComponents();
        
        // Configure active theme
        $this->configureTheme($activeTheme);
        
        // Configure other themes (for cache warming)
        if (config('blaze.cache_warming.enabled')) {
            foreach (config('blaze.cache_warming.themes', []) as $theme) {
                if ($theme !== $activeTheme) {
                    $this->configureTheme($theme);
                }
            }
        }
    }

    /**
     * Configure shared components optimization.
     */
    private function configureSharedComponents(): void
    {
        $sharedConfig = config('blaze.shared_components');
        
        if (! ($sharedConfig['enabled'] ?? false)) {
            return;
        }

        $sharedPath = $sharedConfig['path'] ?? base_path('Modules/Front/Resources/views/components');
        
        if (! is_dir($sharedPath)) {
            return;
        }

        $strategy = $sharedConfig['strategy'] ?? ['compile' => true, 'memo' => false, 'fold' => false];

        Blaze::optimize()
            ->in($sharedPath, 
                compile: $strategy['compile'] ?? true,
                memo: $strategy['memo'] ?? false,
                fold: $strategy['fold'] ?? false
            );
    }

    /**
     * Configure optimization for a specific theme.
     */
    private function configureTheme(string $theme): void
    {
        $themeConfig = config("blaze.themes.{$theme}");
        
        if (! ($themeConfig['enabled'] ?? true)) {
            return;
        }

        $themePath = base_path("Modules/Front/Resources/views/themes/{$theme}");
        
        if (! is_dir($themePath)) {
            return;
        }

        $strategy = $themeConfig['strategy'] ?? ['compile' => true, 'memo' => false, 'fold' => false];
        $componentConfig = $themeConfig['components'] ?? [];

        // Configure components directory
        $componentsPath = $themePath . '/components';
        if (is_dir($componentsPath)) {
            $this->configureComponentDirectory($componentsPath, $strategy, $componentConfig);
        }

        // Configure partials directory
        $partialsPath = $themePath . '/partials';
        if (is_dir($partialsPath)) {
            Blaze::optimize()
                ->in($partialsPath,
                    compile: $strategy['compile'] ?? true,
                    memo: $strategy['memo'] ?? false,
                    fold: false // Never fold partials
                );
        }

        // Configure layouts (compilation only, safer)
        $layoutsPath = $themePath . '/layouts';
        if (is_dir($layoutsPath)) {
            Blaze::optimize()
                ->in($layoutsPath,
                    compile: $strategy['compile'] ?? true,
                    memo: false,
                    fold: false
                );
        }

        // Configure pages (compilation only)
        $pagesPath = $themePath . '/pages';
        if (is_dir($pagesPath)) {
            Blaze::optimize()
                ->in($pagesPath,
                    compile: $strategy['compile'] ?? true,
                    memo: false,
                    fold: false
                );
        }
    }

    /**
     * Configure component directory with pattern-based strategies.
     */
    private function configureComponentDirectory(string $path, array $strategy, array $componentConfig): void
    {
        // Get component-specific configurations
        $memoPatterns = $componentConfig['memo'] ?? [];
        $foldPatterns = $componentConfig['fold'] ?? [];
        $excludePatterns = $componentConfig['exclude'] ?? [];

        // First, configure the base compilation
        Blaze::optimize()
            ->in($path,
                compile: $strategy['compile'] ?? true,
                memo: false,
                fold: false
            );

        // Then handle memoization for specific patterns
        foreach ($memoPatterns as $pattern) {
            $patternPath = $this->resolvePatternPath($path, $pattern);
            if ($patternPath && is_dir($patternPath)) {
                Blaze::optimize()
                    ->in($patternPath,
                        compile: true,
                        memo: true,
                        fold: false
                    );
            }
        }

        // Handle folding for safe components (be very careful!)
        foreach ($foldPatterns as $pattern) {
            $patternPath = $this->resolvePatternPath($path, $pattern);
            if ($patternPath && is_dir($patternPath)) {
                Blaze::optimize()
                    ->in($patternPath,
                        compile: true,
                        memo: false,
                        fold: true
                    );
            }
        }

        // Exclude patterns
        foreach ($excludePatterns as $pattern) {
            $patternPath = $this->resolvePatternPath($path, $pattern);
            if ($patternPath && is_dir($patternPath)) {
                Blaze::optimize()
                    ->in($patternPath, compile: false);
            }
        }
    }

    /**
     * Resolve a pattern (like 'icon*') to actual directory path.
     */
    private function resolvePatternPath(string $basePath, string $pattern): ?string
    {
        // Direct match
        $directPath = $basePath . '/' . $pattern;
        if (is_dir($directPath)) {
            return $directPath;
        }

        // Wildcard pattern (e.g., 'icon*')
        if (str_contains($pattern, '*')) {
            $prefix = str_replace('*', '', $pattern);
            
            $items = scandir($basePath);
            foreach ($items as $item) {
                if ($item === '.' || $item === '..') {
                    continue;
                }
                
                if (str_starts_with($item, $prefix) && is_dir($basePath . '/' . $item)) {
                    return $basePath . '/' . $item;
                }
            }
        }

        return null;
    }

    /**
     * Configure debug mode.
     */
    private function configureDebugMode(): void
    {
        if (! config('blaze.debug')) {
            return;
        }

        Blaze::debug();

        if (config('blaze.throw_exceptions')) {
            Blaze::throw();
        }
    }
}
