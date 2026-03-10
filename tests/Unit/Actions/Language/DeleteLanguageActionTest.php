<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Language;

use InvalidArgumentException;
use Modules\Language\Actions\DeleteLanguageAction;
use Modules\Language\Models\Language;
use Tests\Unit\Actions\ActionTestCase;

class DeleteLanguageActionTest extends ActionTestCase
{
    public function test_execute_deletes_non_default_language(): void
    {
        // Arrange
        $language = Language::create([
            'code' => 'dl' . time(),
            'name' => 'Deletable Language',
            'native_name' => 'Deletable Native',
            'flag' => '🗑️',
            'direction' => 'ltr',
            'sort_order' => 200,
            'is_active' => true,
            'is_default' => false,
        ]);

        $action = new DeleteLanguageAction();

        // Act
        $action->execute($language);

        // Assert
        $this->assertDatabaseMissing('languages', [
            'id' => $language->id,
        ]);
    }

    public function test_execute_throws_exception_when_deleting_default_language(): void
    {
        // Arrange
        $language = Language::create([
            'code' => 'td' . time(),
            'name' => 'Test Default Language',
            'native_name' => 'Test Default',
            'flag' => '🏳️',
            'direction' => 'ltr',
            'sort_order' => 201,
            'is_active' => true,
            'is_default' => true,
        ]);

        $action = new DeleteLanguageAction();

        // Assert & Act
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot delete the default language.');

        $action->execute($language);
    }

    public function test_execute_deletes_language_and_preserves_others(): void
    {
        // Arrange
        $time = time();
        $language1 = Language::create([
            'code' => 'l1' . $time,
            'name' => 'Language One',
            'native_name' => 'Lang One',
            'flag' => '1️⃣',
            'direction' => 'ltr',
            'sort_order' => 202,
            'is_active' => true,
            'is_default' => false,
        ]);

        $language2 = Language::create([
            'code' => 'l2' . $time,
            'name' => 'Language Two',
            'native_name' => 'Lang Two',
            'flag' => '2️⃣',
            'direction' => 'ltr',
            'sort_order' => 203,
            'is_active' => true,
            'is_default' => false,
        ]);

        $action = new DeleteLanguageAction();

        // Act
        $action->execute($language1);

        // Assert
        $this->assertDatabaseMissing('languages', ['id' => $language1->id]);
        $this->assertDatabaseHas('languages', ['id' => $language2->id]);
    }

    public function test_execute_deletes_inactive_language(): void
    {
        // Arrange
        $language = Language::create([
            'code' => 'ia' . time(),
            'name' => 'Inactive Language',
            'native_name' => 'Inactive',
            'flag' => '⚪',
            'direction' => 'ltr',
            'sort_order' => 204,
            'is_active' => false,
            'is_default' => false,
        ]);

        $action = new DeleteLanguageAction();

        // Act
        $action->execute($language);

        // Assert
        $this->assertDatabaseMissing('languages', ['id' => $language->id]);
    }
}
