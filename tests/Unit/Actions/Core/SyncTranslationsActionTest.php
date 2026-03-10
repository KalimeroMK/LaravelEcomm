<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Core;

use InvalidArgumentException;
use Modules\Core\Actions\SyncTranslationsAction;
use Modules\Product\Models\Product;
use Tests\Unit\Actions\ActionTestCase;

class SyncTranslationsActionTest extends ActionTestCase
{
    private SyncTranslationsAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new SyncTranslationsAction();
    }

    public function test_execute_replaces_all_translations(): void
    {
        // Arrange
        $product = Product::factory()->create();
        $product->setTranslation('name', 'en', 'Old English Name');
        $product->setTranslation('name', 'de', 'Old German Name');

        $newTranslations = [
            'en' => [
                'name' => 'New English Name',
                'summary' => 'New English Summary',
            ],
        ];

        // Act
        $result = $this->action->execute($product, $newTranslations);

        // Assert
        $this->assertInstanceOf(Product::class, $result);
        $this->assertDatabaseHas('product_translations', [
            'locale' => 'en',
            'name' => 'New English Name',
        ]);
        $this->assertDatabaseMissing('product_translations', [
            'locale' => 'de',
            'name' => 'Old German Name',
        ]);
    }

    public function test_execute_filters_invalid_locales(): void
    {
        // Arrange
        $product = Product::factory()->create();

        $translations = [
            'en' => [
                'name' => 'English Name',
            ],
            'invalid_locale' => [
                'name' => 'Invalid Name',
            ],
        ];

        // Act
        $result = $this->action->execute($product, $translations);

        // Assert
        $this->assertInstanceOf(Product::class, $result);
        $this->assertDatabaseHas('product_translations', [
            'locale' => 'en',
            'name' => 'English Name',
        ]);
        $this->assertDatabaseMissing('product_translations', [
            'locale' => 'invalid_locale',
        ]);
    }

    public function test_execute_filters_empty_values(): void
    {
        // Arrange
        $product = Product::factory()->create();

        $translations = [
            'en' => [
                'name' => 'Valid Name',
                'summary' => '',
                'description' => null,
            ],
        ];

        // Act
        $result = $this->action->execute($product, $translations);

        // Assert
        $this->assertInstanceOf(Product::class, $result);
        $this->assertDatabaseHas('product_translations', [
            'locale' => 'en',
            'name' => 'Valid Name',
        ]);

        $translation = $product->translation('en');
        $this->assertEmpty($translation->summary);
        $this->assertEmpty($translation->description);
    }

    public function test_delete_for_locale_removes_translation(): void
    {
        // Arrange
        $product = Product::factory()->create();
        $product->setTranslation('name', 'en', 'English Name');
        $product->setTranslation('name', 'de', 'German Name');

        // Act
        $result = $this->action->deleteForLocale($product, 'en');

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseMissing('product_translations', [
            'locale' => 'en',
        ]);
        $this->assertDatabaseHas('product_translations', [
            'locale' => 'de',
        ]);
    }

    public function test_delete_all_removes_all_translations(): void
    {
        // Arrange
        $product = Product::factory()->create();
        $product->setTranslation('name', 'en', 'English Name');
        $product->setTranslation('name', 'de', 'German Name');

        // Act
        $result = $this->action->deleteAll($product);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseMissing('product_translations', [
            'locale' => 'en',
        ]);
        $this->assertDatabaseMissing('product_translations', [
            'locale' => 'de',
        ]);
    }

    public function test_copy_copies_translations_from_source_to_target(): void
    {
        // Arrange
        $product = Product::factory()->create();
        $product->setTranslations('en', [
            'name' => 'English Name',
            'summary' => 'English Summary',
        ]);

        // Act
        $result = $this->action->copy($product, 'en', 'de');

        // Assert
        $this->assertInstanceOf(Product::class, $result);
        $germanTranslation = $product->translation('de');
        $this->assertNotNull($germanTranslation);
        $this->assertEquals('English Name', $germanTranslation->name);
        $this->assertEquals('English Summary', $germanTranslation->summary);
    }

    public function test_copy_returns_original_model_when_source_not_found(): void
    {
        // Arrange
        $product = Product::factory()->create();

        // Act
        $result = $this->action->copy($product, 'nonexistent', 'de');

        // Assert
        $this->assertSame($product, $result);
        $this->assertNull($product->translation('de'));
    }

    public function test_execute_throws_exception_for_non_translatable_model(): void
    {
        // Arrange
        $nonTranslatableModel = new class {
            public $name = 'Test';
        };

        // Assert & Act
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Model must use HasTranslations trait');

        $this->action->execute($nonTranslatableModel, ['en' => ['name' => 'Value']]);
    }

    public function test_delete_for_locale_throws_exception_for_non_translatable_model(): void
    {
        // Arrange
        $nonTranslatableModel = new class {
            public $name = 'Test';
        };

        // Assert & Act
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Model must use HasTranslations trait');

        $this->action->deleteForLocale($nonTranslatableModel, 'en');
    }

    public function test_delete_all_throws_exception_for_non_translatable_model(): void
    {
        // Arrange
        $nonTranslatableModel = new class {
            public $name = 'Test';
        };

        // Assert & Act
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Model must use HasTranslations trait');

        $this->action->deleteAll($nonTranslatableModel);
    }

    public function test_copy_throws_exception_for_non_translatable_model(): void
    {
        // Arrange
        $nonTranslatableModel = new class {
            public $name = 'Test';
        };

        // Assert & Act
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Model must use HasTranslations trait');

        $this->action->copy($nonTranslatableModel, 'en', 'de');
    }

    public function test_execute_handles_multiple_locales(): void
    {
        // Arrange
        $product = Product::factory()->create();

        $translations = [
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
        $result = $this->action->execute($product, $translations);

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
}
