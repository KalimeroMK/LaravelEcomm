<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Message;

use Modules\Message\Actions\MarkAsReadAction;
use Modules\Message\Models\Message;
use Tests\Unit\Actions\ActionTestCase;

class MarkAsReadActionTest extends ActionTestCase
{
    public function test_execute_marks_unread_message_as_read(): void
    {
        // Arrange
        $message = Message::factory()->create([
            'is_read' => false,
        ]);

        $action = new MarkAsReadAction();

        // Act
        $action->execute($message);

        // Assert
        $message->refresh();
        $this->assertTrue($message->is_read);
        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'is_read' => true,
        ]);
    }

    public function test_execute_updates_existing_message(): void
    {
        // Arrange
        $message = Message::factory()->create([
            'is_read' => false,
        ]);

        $this->assertFalse($message->is_read);

        $action = new MarkAsReadAction();

        // Act
        $action->execute($message);

        // Assert
        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'is_read' => true,
        ]);
    }

    public function test_execute_does_not_set_read_at(): void
    {
        // Arrange
        $message = Message::factory()->create([
            'is_read' => false,
            'read_at' => null,
        ]);

        $action = new MarkAsReadAction();

        // Act
        $action->execute($message);

        // Assert
        $message->refresh();
        $this->assertTrue($message->is_read);
        // Note: MarkAsReadAction only sets is_read, not read_at
        $this->assertNull($message->read_at);
    }

    public function test_execute_does_not_fail_on_already_read_message(): void
    {
        // Arrange
        $readAt = now()->subDay();
        $message = Message::factory()->create([
            'is_read' => true,
            'read_at' => $readAt,
        ]);

        $action = new MarkAsReadAction();

        // Act
        $action->execute($message);

        // Assert - should not throw exception
        $message->refresh();
        $this->assertTrue($message->is_read);
    }
}
