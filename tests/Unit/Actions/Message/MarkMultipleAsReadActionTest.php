<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Message;

use Modules\Message\Actions\MarkMultipleAsReadAction;
use Modules\Message\Models\Message;
use Tests\Unit\Actions\ActionTestCase;

class MarkMultipleAsReadActionTest extends ActionTestCase
{
    public function test_execute_marks_multiple_messages_as_read(): void
    {
        // Arrange
        $message1 = Message::factory()->create(['is_read' => false]);
        $message2 = Message::factory()->create(['is_read' => false]);
        $message3 = Message::factory()->create(['is_read' => false]);

        $action = new MarkMultipleAsReadAction();

        // Act
        $result = $action->execute([$message1->id, $message2->id, $message3->id]);

        // Assert
        $this->assertEquals(3, $result);
        $this->assertDatabaseHas('messages', [
            'id' => $message1->id,
            'is_read' => true,
        ]);
        $this->assertDatabaseHas('messages', [
            'id' => $message2->id,
            'is_read' => true,
        ]);
        $this->assertDatabaseHas('messages', [
            'id' => $message3->id,
            'is_read' => true,
        ]);
    }

    public function test_execute_returns_count_of_updated_messages(): void
    {
        // Arrange
        $message1 = Message::factory()->create(['is_read' => false]);
        $message2 = Message::factory()->create(['is_read' => false]);

        $action = new MarkMultipleAsReadAction();

        // Act
        $result = $action->execute([$message1->id, $message2->id]);

        // Assert
        $this->assertEquals(2, $result);
    }

    public function test_execute_marks_only_specified_messages(): void
    {
        // Arrange
        $message1 = Message::factory()->create(['is_read' => false]);
        $message2 = Message::factory()->create(['is_read' => false]);
        $message3 = Message::factory()->create(['is_read' => false]); // Not to be marked

        $action = new MarkMultipleAsReadAction();

        // Act
        $result = $action->execute([$message1->id, $message2->id]);

        // Assert
        $this->assertEquals(2, $result);
        $this->assertDatabaseHas('messages', ['id' => $message1->id, 'is_read' => true]);
        $this->assertDatabaseHas('messages', ['id' => $message2->id, 'is_read' => true]);
        $this->assertDatabaseHas('messages', ['id' => $message3->id, 'is_read' => false]);
    }

    public function test_execute_includes_already_read_messages_in_count(): void
    {
        // Arrange
        $message1 = Message::factory()->create(['is_read' => false]);
        $message2 = Message::factory()->create(['is_read' => true]); // Already read

        $action = new MarkMultipleAsReadAction();

        // Act
        $result = $action->execute([$message1->id, $message2->id]);

        // Assert
        $this->assertEquals(2, $result); // Both are updated (even if one was already read)
    }

    public function test_execute_returns_zero_for_empty_array(): void
    {
        // Arrange
        $action = new MarkMultipleAsReadAction();

        // Act
        $result = $action->execute([]);

        // Assert
        $this->assertEquals(0, $result);
    }

    public function test_execute_handles_nonexistent_ids(): void
    {
        // Arrange
        $message = Message::factory()->create(['is_read' => false]);

        $action = new MarkMultipleAsReadAction();

        // Act
        $result = $action->execute([$message->id, 999999]);

        // Assert
        $this->assertEquals(1, $result); // Only one exists and is updated
        $this->assertDatabaseHas('messages', ['id' => $message->id, 'is_read' => true]);
    }
}
