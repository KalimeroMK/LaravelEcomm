<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Language;

use InvalidArgumentException;
use Modules\Language\Actions\UpdateLanguageAction;
use Modules\Language\DTOs\LanguageDTO;
use Modules\Language\Models\Language;
use Tests\Unit\Actions\ActionTestCase;

class UpdateLanguageActionTest extends ActionTestCase
{
    public function test_execute_updates_language_with_dto(): void
    {
        // Arrange
        $time = time();
        $language = Language::create([
            'code' => 'old' . $time,
            'name' => 'Old Name',
            'native_name' => 'Old Native',
            'flag' => '🔵',
            'direction' => 'ltr',
            'sort_order' => 300,
            'is_active' => true,
            'is_default' => false,
        ]);

        $action = new UpdateLanguageAction();

        $dto = new LanguageDTO(
            code: 'new' . $time,
            name: 'New Name',
            nativeName: 'New Native',
            flag: '🔴',
            direction: 'rtl',
            sortOrder: 301,
            isActive: true,
            isDefault: false,
        );

        // Act
        $result = $action->execute($language, $dto);

        // Assert
        $this->assertInstanceOf(Language::class, $result);
        $this->assertEquals('new' . $time, $result->code);
        $this->assertEquals('New Name', $result->name);
        $this->assertEquals('New Native', $result->native_name);
        $this->assertEquals('🔴', $result->flag);
        $this->assertEquals('rtl', $result->direction);
        $this->assertEquals(301, $result->sort_order);
        $this->assertDatabaseHas('languages', [
            'id' => $language->id,
            'code' => 'new' . $time,
            'name' => 'New Name',
        ]);
    }

    public function test_execute_throws_exception_when_deactivating_default_language(): void
    {
        // Arrange
        $language = Language::create([
            'code' => 'dd' . time(),
            'name' => 'Default Deactivate Test',
            'native_name' => 'Default Deactivate',
            'flag' => '⭐',
            'direction' => 'ltr',
            'sort_order' => 302,
            'is_active' => true,
            'is_default' => true,
        ]);

        $action = new UpdateLanguageAction();

        $dto = new LanguageDTO(
            code: $language->code,
            name: $language->name,
            nativeName: $language->native_name,
            flag: $language->flag,
            direction: $language->direction,
            sortOrder: $language->sort_order,
            isActive: false,
            isDefault: true,
        );

        // Assert & Act
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot deactivate the default language.');

        $action->execute($language, $dto);
    }

    public function test_execute_sets_default_and_unsets_others(): void
    {
        // Arrange
        $time = time();
        $language = Language::create([
            'code' => 'nd' . $time,
            'name' => 'Non Default Language',
            'native_name' => 'Non Default',
            'flag' => '⚪',
            'direction' => 'ltr',
            'sort_order' => 303,
            'is_active' => true,
            'is_default' => false,
        ]);

        // Make sure English exists as default from seeder
        $this->assertDatabaseHas('languages', ['code' => 'en', 'is_default' => true]);

        $action = new UpdateLanguageAction();

        $dto = new LanguageDTO(
            code: $language->code,
            name: 'Now Default',
            nativeName: 'Now Default',
            flag: '⚪',
            direction: 'ltr',
            sortOrder: 303,
            isActive: true,
            isDefault: true,
        );

        // Act
        $result = $action->execute($language, $dto);

        // Assert
        $this->assertTrue($result->is_default);
        // The current language should now be default
        $this->assertDatabaseHas('languages', ['code' => $language->code, 'is_default' => true]);
        // English should no longer be default
        $this->assertDatabaseMissing('languages', ['code' => 'en', 'is_default' => true]);
    }

    public function test_execute_keeps_default_when_not_changing(): void
    {
        // Arrange - Get the existing default language
        $language = Language::where('is_default', true)->first();
        $this->assertNotNull($language);

        $action = new UpdateLanguageAction();

        $dto = new LanguageDTO(
            code: $language->code,
            name: 'Updated Name Only',
            nativeName: $language->native_name,
            flag: $language->flag,
            direction: $language->direction,
            sortOrder: $language->sort_order,
            isActive: true,
            isDefault: true,
        );

        // Act
        $result = $action->execute($language, $dto);

        // Assert
        $this->assertTrue($result->is_default);
        $this->assertEquals('Updated Name Only', $result->name);
    }

    public function test_execute_updates_partial_fields(): void
    {
        // Arrange
        $time = time();
        $language = Language::create([
            'code' => 'pt' . $time,
            'name' => 'Original Name',
            'native_name' => 'Original Native',
            'flag' => '🔵',
            'direction' => 'ltr',
            'sort_order' => 304,
            'is_active' => true,
            'is_default' => false,
        ]);

        $action = new UpdateLanguageAction();

        $dto = new LanguageDTO(
            code: $language->code,
            name: 'Original Name',
            nativeName: 'Updated Native Only',
            flag: '🔵',
            direction: 'ltr',
            sortOrder: 305,
            isActive: true,
            isDefault: false,
        );

        // Act
        $result = $action->execute($language, $dto);

        // Assert
        $this->assertEquals('Original Name', $result->name);
        $this->assertEquals('Updated Native Only', $result->native_name);
        $this->assertEquals(305, $result->sort_order);
    }

    public function test_execute_does_not_unset_defaults_when_already_default(): void
    {
        // Arrange
        $time = time();
        $language = Language::create([
            'code' => 'ad' . $time,
            'name' => 'Already Default Language',
            'native_name' => 'Already Default',
            'flag' => '✓',
            'direction' => 'ltr',
            'sort_order' => 306,
            'is_active' => true,
            'is_default' => true,
        ]);

        // Make sure no other language is default
        Language::where('id', '!=', $language->id)->update(['is_default' => false]);

        $action = new UpdateLanguageAction();

        $dto = new LanguageDTO(
            code: $language->code,
            name: 'Still Default',
            nativeName: 'Already Default',
            flag: '✓',
            direction: 'ltr',
            sortOrder: 306,
            isActive: true,
            isDefault: true,
        );

        // Act
        $result = $action->execute($language, $dto);

        // Assert
        $this->assertTrue($result->is_default);
        $this->assertEquals('Still Default', $result->name);
    }
}
