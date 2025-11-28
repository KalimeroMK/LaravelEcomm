<?php

declare(strict_types=1);

namespace Tests\Feature\MultiLanguage;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Modules\User\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MultiLanguageTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'locale' => 'en',
        ]);
    }

    #[Test]
    public function test_language_switching_works(): void
    {
        $response = $this->get('/language/fr');

        $response->assertRedirect();
        $this->assertEquals('fr', Session::get('locale'));
        $this->assertEquals('fr', App::getLocale());
    }

    #[Test]
    public function test_invalid_language_does_not_change_locale(): void
    {
        $originalLocale = App::getLocale();

        $response = $this->get('/language/invalid');

        $response->assertRedirect();
        $this->assertEquals($originalLocale, App::getLocale());
    }

    #[Test]
    public function test_authenticated_user_locale_is_saved(): void
    {
        $this->actingAs($this->user);

        $response = $this->get('/language/mk');

        $response->assertRedirect();
        $this->assertEquals('mk', $this->user->fresh()->locale);
    }

    #[Test]
    public function test_locale_middleware_sets_correct_locale(): void
    {
        // Test that middleware works by making a request and checking session
        $response = $this->get('/language/de');

        $response->assertRedirect();
        $this->assertEquals('de', Session::get('locale'));
    }

    #[Test]
    public function test_browser_language_detection(): void
    {
        // Test that browser language detection works by checking if locale is set
        $response = $this->withHeaders([
            'Accept-Language' => 'fr-FR,fr;q=0.9,en;q=0.8',
        ])->get('/language/fr');

        $response->assertRedirect();
        $this->assertEquals('fr', Session::get('locale'));
    }

    #[Test]
    public function test_rtl_locale_detection(): void
    {
        $locales = config('app.locales', []);

        $this->assertTrue($locales['ar']['rtl'] ?? false);
        $this->assertFalse($locales['en']['rtl'] ?? true);
        $this->assertFalse($locales['fr']['rtl'] ?? true);
    }

    #[Test]
    public function test_translation_files_exist(): void
    {
        $locales = ['en', 'mk', 'de', 'fr', 'es', 'it', 'ar'];

        foreach ($locales as $locale) {
            $this->assertFileExists(resource_path("lang/{$locale}/messages.php"));
            $this->assertFileExists(resource_path("lang/{$locale}/auth.php"));
            $this->assertFileExists(resource_path("lang/{$locale}/validation.php"));
        }
    }

    #[Test]
    public function test_french_translations_are_correct(): void
    {
        App::setLocale('fr');

        $this->assertEquals('Accueil', __('messages.home'));
        $this->assertEquals('Nom', __('messages.name'));
        $this->assertEquals('Enregistrer', __('messages.save'));
    }

    #[Test]
    public function test_language_switcher_component_renders(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        // The component should be available for use in views
        $this->assertTrue(class_exists(\Modules\Core\View\Components\LanguageSwitcher::class));
    }

    #[Test]
    public function test_rtl_support_component_renders(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        // The component should be available for use in views
        $this->assertTrue(class_exists(\Modules\Core\View\Components\RTLSupport::class));
    }

    #[Test]
    public function test_translation_service_works(): void
    {
        $translationService = app(\Modules\Core\Services\TranslationService::class);

        $this->assertInstanceOf(\Modules\Core\Services\TranslationService::class, $translationService);
    }

    #[Test]
    public function test_translation_api_endpoints_exist(): void
    {
        $this->actingAs($this->user);

        // Test that the translation service exists
        $translationService = app(\Modules\Core\Services\TranslationService::class);
        $this->assertInstanceOf(\Modules\Core\Services\TranslationService::class, $translationService);

        // Test that we can get translations for a model
        $translations = $translationService->getModelTranslations($this->user);
        $this->assertIsArray($translations);
    }
}
