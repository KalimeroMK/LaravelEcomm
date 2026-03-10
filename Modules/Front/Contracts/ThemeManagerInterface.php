<?php

declare(strict_types=1);

namespace Modules\Front\Contracts;

/**
 * Theme Manager Interface
 *
 * Defines the contract for theme management with Blaze optimization support.
 */
interface ThemeManagerInterface
{
    /**
     * Get the currently active theme.
     */
    public function getActiveTheme(): string;

    /**
     * Set a new active theme.
     */
    public function setActiveTheme(string $theme): void;

    /**
     * Get all available themes.
     */
    public function getAvailableThemes(): array;

    /**
     * Check if a theme exists.
     */
    public function themeExists(string $theme): bool;

    /**
     * Check if Blaze is enabled globally.
     */
    public function isBlazeEnabled(): bool;

    /**
     * Check if Blaze is enabled for a specific theme.
     */
    public function isBlazeEnabledForTheme(string $theme): bool;

    /**
     * Check if Blaze is enabled for current theme.
     */
    public function isBlazeEnabledForCurrentTheme(): bool;

    /**
     * Get Blaze strategy for current theme.
     */
    public function getBlazeStrategy(): array;

    /**
     * Get detailed Blaze status for debugging.
     */
    public function getBlazeStatus(): array;

    /**
     * Pre-warm Blaze cache for all views in active theme.
     */
    public function prewarmBlazeCache(?string $theme = null): array;

    /**
     * Clear Blaze cache for a theme.
     */
    public function clearBlazeCache(?string $theme = null): array;

    /**
     * Warm cache for all configured themes.
     */
    public function warmAllThemes(): array;
}
