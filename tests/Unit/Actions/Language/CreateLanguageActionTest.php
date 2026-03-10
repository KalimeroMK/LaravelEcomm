<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Language;

use Modules\Language\Actions\CreateLanguageAction;
use Modules\Language\DTOs\LanguageDTO;
use Modules\Language\Models\Language;
use Tests\Unit\Actions\ActionTestCase;

class CreateLanguageActionTest extends ActionTestCase
{
    public function test_execute_creates_language_with_dto(): void
    {
        // Arrange
        $action = new CreateLanguageAction();
        $uniqueCode = 'xx' . time();

        $dto = new LanguageDTO(
            code: $uniqueCode,
            name: 'Test Language',
            nativeName: 'Test Native',
            flag: '🏳️',
            direction: 'ltr',
            sortOrder: 100,
            isActive: true,
            isDefault: false,
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(Language::class, $result);
        $this->assertEquals($uniqueCode, $result->code);
        $this->assertEquals('Test Language', $result->name);
        $this->assertEquals('Test Native', $result->native_name);
        $this->assertEquals('🏳️', $result->flag);
        $this->assertEquals('ltr', $result->direction);
        $this->assertEquals(100, $result->sort_order);
        $this->assertTrue($result->is_active);
        $this->assertFalse($result->is_default);
        $this->assertDatabaseHas('languages', [
            'code' => $uniqueCode,
            'name' => 'Test Language',
        ]);
    }

    public function test_execute_creates_inactive_language(): void
    {
        // Arrange
        $action = new CreateLanguageAction();
        $uniqueCode = 'xy' . time();

        $dto = new LanguageDTO(
            code: $uniqueCode,
            name: 'Inactive Test Language',
            nativeName: 'Inactive Native',
            flag: '⚪',
            direction: 'ltr',
            sortOrder: 101,
            isActive: false,
            isDefault: false,
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertFalse($result->is_active);
        $this->assertDatabaseHas('languages', [
            'code' => $uniqueCode,
            'is_active' => false,
        ]);
    }

    public function test_execute_creates_rtl_language(): void
    {
        // Arrange
        $action = new CreateLanguageAction();
        $uniqueCode = 'rt' . time();

        $dto = new LanguageDTO(
            code: $uniqueCode,
            name: 'RTL Test Language',
            nativeName: 'RTL Native',
            flag: '🏴',
            direction: 'rtl',
            sortOrder: 102,
            isActive: true,
            isDefault: false,
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertEquals('rtl', $result->direction);
        $this->assertDatabaseHas('languages', [
            'code' => $uniqueCode,
            'direction' => 'rtl',
        ]);
    }

    public function test_execute_sets_default_and_unsets_others(): void
    {
        // Arrange
        $action = new CreateLanguageAction();
        $uniqueCode = 'df' . time();

        // Verify there's an existing default
        $this->assertDatabaseHas('languages', ['is_default' => true]);

        $dto = new LanguageDTO(
            code: $uniqueCode,
            name: 'New Default Language',
            nativeName: 'New Default',
            flag: '🎯',
            direction: 'ltr',
            sortOrder: 103,
            isActive: true,
            isDefault: true,
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertTrue($result->is_default);
        $this->assertDatabaseHas('languages', ['code' => $uniqueCode, 'is_default' => true]);
        // The new language should now be default, previous defaults should be unset
        $this->assertDatabaseMissing('languages', ['code' => 'en', 'is_default' => true]);
    }

    public function test_execute_creates_language_without_flag(): void
    {
        // Arrange
        $action = new CreateLanguageAction();
        $uniqueCode = 'nf' . time();

        $dto = new LanguageDTO(
            code: $uniqueCode,
            name: 'No Flag Language',
            nativeName: 'No Flag Native',
            flag: null,
            direction: 'ltr',
            sortOrder: 104,
            isActive: true,
            isDefault: false,
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertNull($result->flag);
        $this->assertDatabaseHas('languages', [
            'code' => $uniqueCode,
            'flag' => null,
        ]);
    }
}
