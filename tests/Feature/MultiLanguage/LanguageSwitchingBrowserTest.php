<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Models\User;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'locale' => 'en',
    ]);
});

test('language switcher component exists', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    // Check if language switcher component class exists
    $this->assertTrue(class_exists(Modules\Core\View\Components\LanguageSwitcher::class));
});

test('user can switch language from English to French', function () {
    $response = $this->get('/language/fr');

    $response->assertRedirect();
    $response->assertSessionHas('locale', 'fr');
});

test('user can switch language from English to Macedonian', function () {
    $response = $this->get('/language/mk');

    $response->assertRedirect();
    $response->assertSessionHas('locale', 'mk');
});

test('user can switch language from English to German', function () {
    $response = $this->get('/language/de');

    $response->assertRedirect();
    $response->assertSessionHas('locale', 'de');
});

test('user can switch language from English to Arabic', function () {
    $response = $this->get('/language/ar');

    $response->assertRedirect();
    $response->assertSessionHas('locale', 'ar');
});

test('invalid language does not change locale', function () {
    $originalLocale = app()->getLocale();

    $response = $this->get('/language/invalid');

    $response->assertRedirect();
    $this->assertEquals($originalLocale, app()->getLocale());
});

test('authenticated user locale preference is saved', function () {
    $this->actingAs($this->user);

    $response = $this->get('/language/fr');

    $response->assertRedirect();
    $this->assertEquals('fr', $this->user->fresh()->locale);
});

test('language switching works with multiple requests', function () {
    // Switch to French
    $response = $this->get('/language/fr');
    $response->assertSessionHas('locale', 'fr');

    // Switch to German
    $response = $this->get('/language/de');
    $response->assertSessionHas('locale', 'de');

    // Switch to Macedonian
    $response = $this->get('/language/mk');
    $response->assertSessionHas('locale', 'mk');
});

test('RTL language sets correct direction', function () {
    $response = $this->get('/language/ar');

    $response->assertRedirect();
    $response->assertSessionHas('locale', 'ar');

    // Check if RTL is detected correctly
    $locales = config('app.locales', []);
    $this->assertTrue($locales['ar']['rtl'] ?? false);
});

test('LTR language sets correct direction', function () {
    $response = $this->get('/language/fr');

    $response->assertRedirect();
    $response->assertSessionHas('locale', 'fr');

    // Check if LTR is detected correctly
    $locales = config('app.locales', []);
    $this->assertFalse($locales['fr']['rtl'] ?? true);
});

test('language switching preserves user session', function () {
    $this->actingAs($this->user);

    // Set some session data
    session(['test_data' => 'preserved']);

    $response = $this->get('/language/de');

    $response->assertRedirect();
    $response->assertSessionHas('locale', 'de');
    $response->assertSessionHas('test_data', 'preserved');
});

test('language switching works from any page', function () {
    // Test from different pages
    $pages = ['/', '/about-us', '/contact', '/product-grids'];

    foreach ($pages as $page) {
        $response = $this->get($page);

        if ($response->status() === 200) {
            // Try to switch language from this page
            $langResponse = $this->get('/language/fr');
            $langResponse->assertRedirect();
            $langResponse->assertSessionHas('locale', 'fr');

            // Reset for next test
            $this->get('/language/en');
        }
    }
});

test('language switcher shows correct current language', function () {
    // Set French as current language
    $this->get('/language/fr');

    $response = $this->get('/');

    $response->assertStatus(200);
    // Check that French locale is set in session
    $response->assertSessionHas('locale', 'fr');
    $this->assertEquals('fr', app()->getLocale());
});

test('language switcher shows all available languages', function () {
    $response = $this->get('/');

    $response->assertStatus(200);

    // Check if all language configurations exist
    $locales = config('app.locales', []);
    $this->assertCount(7, $locales); // en, mk, de, fr, es, it, ar

    // Check that all locales have required properties
    foreach ($locales as $locale => $config) {
        $this->assertArrayHasKey('name', $config);
        $this->assertArrayHasKey('native', $config);
        $this->assertArrayHasKey('flag', $config);
        $this->assertArrayHasKey('rtl', $config);
    }
});

test('language switching updates app locale immediately', function () {
    $this->assertEquals('en', app()->getLocale());

    $response = $this->get('/language/fr');

    $response->assertRedirect();
    $this->assertEquals('fr', app()->getLocale());
});

test('language switching works with query parameters', function () {
    $response = $this->get('/language/de?redirect=/product-grids');

    $response->assertRedirect();
    $response->assertSessionHas('locale', 'de');
});

test('language switching works with referer header', function () {
    $response = $this->withHeaders([
        'Referer' => '/product-grids',
    ])->get('/language/mk');

    $response->assertRedirect();
    $response->assertSessionHas('locale', 'mk');
});

test('multiple language switches in same session work correctly', function () {
    $languages = ['fr', 'de', 'mk', 'ar', 'es', 'it'];

    foreach ($languages as $lang) {
        $response = $this->get("/language/{$lang}");
        $response->assertRedirect();
        $response->assertSessionHas('locale', $lang);
        $this->assertEquals($lang, app()->getLocale());
    }
});

test('language switching maintains authentication state', function () {
    $this->actingAs($this->user);

    $response = $this->get('/language/fr');

    $response->assertRedirect();
    $response->assertSessionHas('locale', 'fr');

    // User should still be authenticated
    $this->assertTrue(auth()->check());
    $this->assertEquals($this->user->id, auth()->id());
});

test('language switching works with different user agents', function () {
    $userAgents = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
        'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36',
    ];

    foreach ($userAgents as $userAgent) {
        $response = $this->withHeaders([
            'User-Agent' => $userAgent,
        ])->get('/language/de');

        $response->assertRedirect();
        $response->assertSessionHas('locale', 'de');
    }
});
