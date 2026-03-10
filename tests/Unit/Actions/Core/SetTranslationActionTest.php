<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Core;

use InvalidArgumentException;
use Modules\Core\Actions\SetTranslationAction;
use Modules\Product\Models\Product;
use Tests\Unit\Actions\ActionTestCase;

class SetTranslationActionTest extends ActionTestCase
{
    private SetTranslationAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new SetTranslationAction();
    }

    public function test_set_field_creates_translation(): void
    {
        // Arrange
        $product = Product::factory()->create();

        // Act
        $result = $this->action->setField($product, 'name', 'en', 'English Product Name');

        // Assert
        $this->assertInstanceOf(Product::class, $result);
        $this->assertDatabaseHas('product_translations', [
            'locale' => 'en',
            'name' => 'English Product Name',
        ]);
    }

    public function test_set_field_updates_existing_translation(): void
    {
        // Arrange
        $product = Product::factory()->create();
        $product->setTranslation('name', 'en', 'Old Name');

        // Act
        $result = $this->action->setField($product, 'name', 'en', 'Updated Name');

        // Assert
        $this->assertInstanceOf(Product::class, $result);
        $this->assertDatabaseHas('product_translations', [
            'locale' => 'en',
            'name' => 'Updated Name',
        ]);
        $this->assertDatabaseMissing('product_translations', [
            'locale' => 'en',
            'name' => 'Old Name',
        ]);
    }

    public function test_set_for_locale_creates_multiple_fields(): void
    {
        // Arrange
        $product = Product::factory()->create();
        $translations = [
            'name' => 'Product Name',
            'summary' => 'Product Summary',
            'description' => 'Product Description',
        ];

        // Act
        $result = $this->action->setForLocale($product, 'en', $translations);

        // Assert
        $this->assertInstanceOf(Product::class, $result);
        $this->assertDatabaseHas('product_translations', [
            'locale' => 'en',
            'name' => 'Product Name',
            'summary' => 'Product Summary',
            'description' => 'Product Description',
        ]);
    }

    public function test_set_multiple_creates_translations_for_multiple_locales(): void
    {
        // Arrange
        $product = Product::factory()->create();
        $translationsByLocale = [
            'en' => [
                'name' => 'English Name',
                'summary' => 'English Summary',
            ],
            'de' => [
                'name' => 'German Name',
                'summary' => 'German Summary',
            ],
        ];

        // Act
        $result = $this->action->setMultiple($product, $translationsByLocale);

        // Assert
        $this->assertInstanceOf(Product::class, $result);
        $this->assertDatabaseHas('product_translations', [
            'locale' => 'en',
            'name' => 'English Name',
        ]);
        $this->assertDatabaseHas('product_translations', [
            'locale' => 'de',
            'name' => 'German Name',
        ]);
    }

    public function test_set_field_throws_exception_for_non_translatable_model(): void
    {
        // Arrange
        $nonTranslatableModel = new class {
            public $name = 'Test';
        };

        // Assert & Act
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Model must use HasTranslations trait');

        $this->action->setField($nonTranslatableModel, 'name', 'en', 'Value');
    }

    public function test_set_for_locale_throws_exception_for_non_translatable_model(): void
    {
        // Arrange
        $nonTranslatableModel = new class {
            public $name = 'Test';
        };

        // Assert & Act
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Model must use HasTranslations trait');

        $this->action->setForLocale($nonTranslatableModel, 'en', ['name' => 'Value']);
    }

    public function test_set_multiple_throws_exception_for_non_translatable_model(): void
    {
        // Arrange
        $nonTranslatableModel = new class {
            public $name = 'Test';
        };

        // Assert & Act
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Model must use HasTranslations trait');

        $this->action->setMultiple($nonTranslatableModel, ['en' => ['name' => 'Value']]);
    }

    public function test_set_for_locale_returns_same_model_instance(): void
    {
        // Arrange
        $product = Product::factory()->create();

        // Act
        $result = $this->action->setForLocale($product, 'en', ['name' => 'Test Name']);

        // Assert
        $this->assertSame($product, $result);
    }

    public function test_set_field_differentiates_locales(): void
    {
        // Arrange
        $product = Product::factory()->create();

        // Act
        $this->action->setField($product, 'name', 'en', 'English Name');
        $this->action->setField($product, 'name', 'de', 'German Name');

        // Assert
        $englishTranslation = $product->translation('en');
        $germanTranslation = $product->translation('de');

        $this->assertEquals('English Name', $englishTranslation->name);
        $this->assertEquals('German Name', $germanTranslation->name);
    }
}
