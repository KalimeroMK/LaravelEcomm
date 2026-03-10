<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Message;

use Modules\Message\Actions\DeleteMessageAction;
use Modules\Message\Models\Message;
use Modules\Message\Repository\MessageRepository;
use Tests\Unit\Actions\ActionTestCase;

class DeleteMessageActionTest extends ActionTestCase
{
    public function test_execute_deletes_existing_message(): void
    {
        // Arrange
        $message = Message::factory()->create([
            'name' => 'Message to Delete',
            'subject' => 'Delete Me',
            'email' => 'delete@example.com',
        ]);

        $repository = new MessageRepository();
        $action = new DeleteMessageAction($repository);

        // Act
        $action->execute($message->id);

        // Assert
        $this->assertDatabaseMissing('messages', [
            'id' => $message->id,
            'name' => 'Message to Delete',
        ]);
    }

    public function test_execute_deletes_message_and_verifies_count(): void
    {
        // Arrange
        $message1 = Message::factory()->create();
        $message2 = Message::factory()->create();
        $message3 = Message::factory()->create();

        $repository = new MessageRepository();
        $action = new DeleteMessageAction($repository);

        // Act
        $action->execute($message2->id);

        // Assert
        $this->assertDatabaseHas('messages', ['id' => $message1->id]);
        $this->assertDatabaseMissing('messages', ['id' => $message2->id]);
        $this->assertDatabaseHas('messages', ['id' => $message3->id]);
        $this->assertEquals(2, Message::count());
    }

    public function test_execute_deletes_message_with_parent_id(): void
    {
        // Arrange
        $parentMessage = Message::factory()->create();
        $childMessage = Message::factory()->create([
            'parent_id' => $parentMessage->id,
        ]);

        $repository = new MessageRepository();
        $action = new DeleteMessageAction($repository);

        // Act
        $action->execute($childMessage->id);

        // Assert
        $this->assertDatabaseMissing('messages', ['id' => $childMessage->id]);
        $this->assertDatabaseHas('messages', ['id' => $parentMessage->id]);
    }
}
