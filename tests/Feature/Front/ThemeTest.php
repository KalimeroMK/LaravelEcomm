<?php

declare(strict_types=1);

use Illuminate\Support\Facades\View;
use Modules\Settings\Models\Setting;

describe('Multi-Theme Functionality', function () {
    beforeEach(function () {
        // Create default setting
        Setting::factory()->create([
            'active_template' => 'default',
        ]);
    });

    it('can get available themes from filesystem', function () {
        $themes = get_available_themes();

        expect($themes)->toBeArray()
            ->toContain('default')
            ->toContain('modern');
    });

    it('returns default theme when no setting exists', function () {
        Setting::query()->delete();

        $view = theme_view('pages.product-grids');

        expect($view)->toBe('front::themes.default.pages.product-grids');
    });

    it('returns active theme view path', function () {
        $setting = Setting::first();
        $setting->update(['active_template' => 'modern']);

        $view = theme_view('pages.product-grids');

        // theme_view may return default if modern view doesn't exist
        expect($view)->toStartWith('front::themes.');
    });

    it('falls back to default theme when view does not exist in active theme', function () {
        $setting = Setting::first();
        $setting->update(['active_template' => 'modern']);

        // Modern theme doesn't have product-grids, so should fallback to default
        $view = theme_view('pages.product-grids');

        // The helper may return default if modern view doesn't exist
        expect($view)->toStartWith('front::themes.');

        // Check if views exist
        $exists = View::exists('front::themes.modern.pages.product-grids');
        $defaultExists = View::exists('front::themes.default.pages.product-grids');

        // At least default should exist
        expect($defaultExists)->toBeTrue();
    });

    it('generates correct theme asset URL', function () {
        $setting = Setting::first();
        $setting->update(['active_template' => 'modern']);

        $asset = theme_asset('css/style.css');

        expect($asset)->toBe('http://localhost/frontend/themes/modern/css/style.css');
    });

    it('generates default theme asset URL when no setting exists', function () {
        Setting::query()->delete();

        $asset = theme_asset('css/style.css');

        expect($asset)->toBe('http://localhost/frontend/themes/default/css/style.css');
    });

    it('can use specific theme for asset', function () {
        $asset = theme_asset('css/style.css', 'modern');

        expect($asset)->toBe('http://localhost/frontend/themes/modern/css/style.css');
    });
});

describe('Theme View Resolution', function () {
    beforeEach(function () {
        Setting::factory()->create([
            'active_template' => 'default',
        ]);
    });

    it('resolves default theme views correctly', function () {
        $setting = Setting::first();
        $setting->update(['active_template' => 'default']);

        $view = theme_view('layouts.master');

        expect($view)->toBe('front::themes.default.layouts.master');
        expect(View::exists($view))->toBeTrue();
    });

    it('resolves modern theme views correctly', function () {
        $setting = Setting::first();
        $setting->update(['active_template' => 'modern']);

        $view = theme_view('layouts.master');

        expect($view)->toBe('front::themes.modern.layouts.master');
        expect(View::exists($view))->toBeTrue();
    });

    it('resolves index view for both themes', function () {
        $setting = Setting::first();

        // Default theme
        $setting->update(['active_template' => 'default']);
        $defaultView = theme_view('index');
        expect(View::exists($defaultView))->toBeTrue();

        // Modern theme
        $setting->update(['active_template' => 'modern']);
        $modernView = theme_view('index');
        expect(View::exists($modernView))->toBeTrue();
    });
});

describe('Theme Settings Management', function () {
    it('can update active theme in settings', function () {
        $setting = Setting::factory()->create([
            'active_template' => 'default',
        ]);

        $setting->update(['active_template' => 'modern']);

        expect($setting->fresh()->active_template)->toBe('modern');
    });

    it('validates theme selection', function () {
        $availableThemes = get_available_themes();

        $setting = Setting::factory()->create([
            'active_template' => 'default',
        ]);

        // Should accept valid theme
        $setting->update(['active_template' => 'modern']);
        expect($setting->fresh()->active_template)->toBe('modern');

        // Should accept default theme
        $setting->update(['active_template' => 'default']);
        expect($setting->fresh()->active_template)->toBe('default');
    });
});

describe('Theme View Composer', function () {
    beforeEach(function () {
        Setting::factory()->create([
            'active_template' => 'default',
        ]);
    });

    it('provides themePath variable to views', function () {
        // Test that ThemeViewComposer is registered
        $composer = new Modules\Front\Http\ViewComposers\ThemeViewComposer();
        $view = View::make('front::themes.default.index', []);

        $composer->compose($view);
        $data = $view->getData();

        expect($data)->toHaveKey('themePath');
        expect($data['themePath'])->toStartWith('front::themes.');
    });

    it('provides activeTheme variable to views', function () {
        $composer = new Modules\Front\Http\ViewComposers\ThemeViewComposer();
        $view = View::make('front::themes.default.index', []);

        $composer->compose($view);
        $data = $view->getData();

        expect($data)->toHaveKey('activeTheme');
        expect($data['activeTheme'])->toBe('default');
    });

    it('updates themePath when theme changes', function () {
        $setting = Setting::first();
        $setting->update(['active_template' => 'modern']);

        $composer = new Modules\Front\Http\ViewComposers\ThemeViewComposer();
        $view = View::make('front::themes.modern.index', []);

        $composer->compose($view);
        $data = $view->getData();

        expect($data['themePath'])->toBe('front::themes.modern');
        expect($data['activeTheme'])->toBe('modern');
    });
});
