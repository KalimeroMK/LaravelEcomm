<?php

declare(strict_types=1);

namespace Modules\Front\Services\Theme;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Livewire\Blaze\Blaze;
use Modules\Front\Contracts\ThemeManagerInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Finder\Finder;

/**
 * Theme Manager with Blaze Optimization Support
 *
 * Manages theme switching, view resolution, and Blaze cache warming.
 */
class ThemeManager implements ThemeManagerInterface
{
    private string $activeTheme;
    private ?array $availableThemes = null;

    public function __construct()
    {
        $this->activeTheme = $this->resolveActiveTheme();
    }

    public function getActiveTheme(): string
    {
        return $this->activeTheme;
    }

    public function setActiveTheme(string $theme): void
    {
        if (! $this->themeExists($theme)) {
            throw new \InvalidArgumentException("Theme '{$theme}' does not exist.");
        }

        $this->activeTheme = $theme;
        
        if (config('blaze.enabled')) {
            $this->reconfigureBlaze();
        }
    }

    public function getAvailableThemes(): array
    {
        if ($this->availableThemes !== null) {
            return $this->availableThemes;
        }

        $themesPath = base_path('Modules/Front/Resources/views/themes');
        
        if (! is_dir($themesPath)) {
            return [];
        }

        $themes = [];
        $items = scandir($themesPath);
        
        foreach ($items as $item) {
            if ($item === '.' || $item === '..' || ! is_dir($themesPath . '/' . $item)) {
                continue;
            }

            $themes[] = [
                'name' => $item,
                'path' => $themesPath . '/' . $item,
                'enabled' => config("blaze.themes.{$item}.enabled", true),
                'blaze_enabled' => $this->isBlazeEnabledForTheme($item),
                'strategy' => config("blaze.themes.{$item}.strategy", []),
            ];
        }

        $this->availableThemes = $themes;
        return $themes;
    }

    public function themeExists(string $theme): bool
    {
        $path = base_path("Modules/Front/Resources/views/themes/{$theme}");
        return is_dir($path);
    }

    public function isBlazeEnabled(): bool
    {
        return config('blaze.enabled', true);
    }

    public function isBlazeEnabledForTheme(string $theme): bool
    {
        if (! $this->isBlazeEnabled()) {
            return false;
        }

        return config("blaze.themes.{$theme}.enabled", true)
            && config("blaze.themes.{$theme}.strategy.compile", true);
    }

    public function isBlazeEnabledForCurrentTheme(): bool
    {
        return $this->isBlazeEnabledForTheme($this->activeTheme);
    }

    public function getBlazeStrategy(): array
    {
        return config("blaze.themes.{$this->activeTheme}.strategy", [
            'compile' => true,
            'memo' => false,
            'fold' => false,
        ]);
    }

    public function getBlazeStatus(): array
    {
        return [
            'enabled' => $this->isBlazeEnabled(),
            'debug' => config('blaze.debug'),
            'active_theme' => $this->activeTheme,
            'theme_enabled' => $this->isBlazeEnabledForCurrentTheme(),
            'strategy' => $this->getBlazeStrategy(),
            'view_share_support' => config('blaze.view_share_support'),
            'view_composer_support' => config('blaze.view_composer_support'),
            'cache_warming_enabled' => config('blaze.cache_warming.enabled'),
        ];
    }

    public function prewarmBlazeCache(?string $theme = null): array
    {
        $theme = $theme ?? $this->activeTheme;
        
        if (! $this->isBlazeEnabledForTheme($theme)) {
            return [
                'success' => false,
                'message' => "Blaze is not enabled for theme '{$theme}'",
                'compiled' => 0,
                'failed' => 0,
            ];
        }

        $themePath = base_path("Modules/Front/Resources/views/themes/{$theme}");
        
        if (! is_dir($themePath)) {
            return [
                'success' => false,
                'message' => "Theme path not found: {$themePath}",
                'compiled' => 0,
                'failed' => 0,
            ];
        }

        $compiled = 0;
        $failed = 0;
        $errors = [];

        $finder = new Finder();
        $finder->files()->in($themePath)->name('*.blade.php');

        foreach ($finder as $file) {
            try {
                $relativePath = $file->getRelativePathname();
                $viewName = $this->filePathToViewName($theme, $relativePath);
                app('view')->make($viewName)->render();
                $compiled++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = [
                    'file' => $file->getRelativePathname(),
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'success' => true,
            'theme' => $theme,
            'compiled' => $compiled,
            'failed' => $failed,
            'errors' => $errors,
        ];
    }

    public function clearBlazeCache(?string $theme = null): array
    {
        $theme = $theme ?? $this->activeTheme;

        // Use direct filesystem operation instead of Artisan::call('view:clear')
        // to avoid bootstrapping the full Artisan kernel inside a web request.
        $compiledPath = config('view.compiled');
        if ($compiledPath && is_dir($compiledPath)) {
            \Illuminate\Support\Facades\File::cleanDirectory($compiledPath);
        }

        if (config('blaze.cache_warming.enabled')) {
            \Illuminate\Support\Facades\Cache::forget('active_theme');
        }

        return [
            'success' => true,
            'theme' => $theme,
            'message' => "Blaze cache cleared for theme '{$theme}'",
        ];
    }

    public function warmAllThemes(): array
    {
        $results = [];
        $themes = config('blaze.cache_warming.themes', []);

        foreach ($themes as $theme) {
            if ($this->isBlazeEnabledForTheme($theme)) {
                $results[$theme] = $this->prewarmBlazeCache($theme);
            }
        }

        return $results;
    }

    private function reconfigureBlaze(): void
    {
        $compiledPath = config('view.compiled');
        if ($compiledPath && is_dir($compiledPath)) {
            \Illuminate\Support\Facades\File::cleanDirectory($compiledPath);
        }
    }

    private function resolveActiveTheme(): string
    {
        try {
            $setting = app('settings');
            $theme = $setting->active_template ?? 'default';
            
            if (! $this->themeExists($theme)) {
                return 'default';
            }
            
            return $theme;
        } catch (\Exception $e) {
            return 'default';
        }
    }

    private function filePathToViewName(string $theme, string $relativePath): string
    {
        $path = str_replace('.blade.php', '', $relativePath);
        $path = str_replace(['/', '\\'], '.', $path);
        return "front::themes.{$theme}.{$path}";
    }
}
