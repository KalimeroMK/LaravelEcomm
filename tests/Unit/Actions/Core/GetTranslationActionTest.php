<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Core;

use InvalidArgumentException;
use Modules\Core\Actions\GetTranslationAction;
use Modules\Core\Traits\HasTranslations;
use Modules\Product\Models\Product;
use Tests\Unit\Actions\ActionTestCase;

class GetTranslationActionTest extends ActionTestCase
{
    private GetTranslationAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new GetTranslationAction();
    }

    public function test_get_field_returns_translation_for_specific_locale(): void
    {
        // Arrange
        $product = Product::factory()->create();
        $product->setTranslation('name', 'en', 'English Product Name');
        $product->setTranslation('name', 'de', 'German Product Name');

        // Act
        $result = $this->action->getField($product, 'name', 'en', false);

        // Assert
        $this->assertEquals('English Product Name', $result);
    }

    public function test_get_field_returns_translation_for_current_locale(): void
    {
        // Arrange
        $product = Product::factory()->create();
        $product->setTranslation('name', 'en', 'English Product Name');
        app()->setLocale('en');

        // Act
        $result = $this->action->getField($product, 'name', null, false);

        // Assert
        $this->assertEquals('English Product Name', $result);
    }

    public function test_get_field_uses_fallback_when_enabled(): void
    {
        // Arrange
        $product = Product::factory()->create();
        $product->setTranslation('name', 'en', 'English Product Name');

        // Act - request a locale that doesn't have translation but with fallback
        $result = $this->action->getField($product, 'name', 'de', true);

        // Assert - should fallback to English
        $this->assertEquals('English Product Name', $result);
    }

    public function test_get_field_returns_null_without_fallback(): void
    {
        // Arrange
        $product = Product::factory()->create();
        $product->setTranslation('name', 'en', 'English Product Name');

        // Act - request a locale that doesn't have translation without fallback
        $result = $this->action->getField($product, 'name', 'de', false);

        // Assert - should return null since no German translation exists
        $this->assertNull($result);
    }

    public function test_get_all_for_locale_returns_all_translated_fields(): void
    {
        // Arrange
        $product = Product::factory()->create();
        $product->setTranslations('en', [
            'name' => 'English Name',
            'summary' => 'English Summary',
        ]);

        // Act
        $result = $this->action->getAllForLocale($product, 'en', false);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('summary', $result);
        $this->assertArrayHasKey('description', $result);
        $this->assertArrayHasKey('slug', $result);
        $this->assertArrayHasKey('meta_title', $result);
        $this->assertArrayHasKey('meta_description', $result);
    }

    public function test_get_all_for_model_returns_all_locales(): void
    {
        // Arrange
        $product = Product::factory()->create();
        $product->setTranslation('name', 'en', 'English Name');
        $product->setTranslation('name', 'de', 'German Name');

        // Act
        $result = $this->action->getAllForModel($product, ['en', 'de']);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('en', $result);
        $this->assertArrayHasKey('de', $result);
        $this->assertEquals('English Name', $result['en']['name']);
        $this->assertEquals('German Name', $result['de']['name']);
    }

    public function test_to_array_returns_model_with_translations(): void
    {
        // Arrange
        $product = Product::factory()->create([
            'title' => 'Test Product',
        ]);
        $product->setTranslation('name', 'en', 'English Name');

        // Act
        $result = $this->action->toArray($product);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('translations', $result);
    }

    public function test_throws_exception_for_non_translatable_model(): void
    {
        // Arrange
        $nonTranslatableModel = new class {
            public $name = 'Test';
        };

        // Assert & Act
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Model must use HasTranslations trait');

        $this->action->getField($nonTranslatableModel, 'name', 'en');
    }

    public function test_get_all_for_locale_uses_current_locale_when_null(): void
    {
        // Arrange
        $product = Product::factory()->create();
        $product->setTranslation('name', 'de', 'German Product Name');
        app()->setLocale('de');

        // Act
        $result = $this->action->getAllForLocale($product, null, false);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('name', $result);
    }

    public function test_get_all_for_model_uses_active_locales_when_null(): void
    {
        // Arrange
        $product = Product::factory()->create();
        $product->setTranslation('name', 'en', 'English Name');

        // Act
        $result = $this->action->getAllForModel($product, null);

        // Assert
        $this->assertIsArray($result);
    }
}
