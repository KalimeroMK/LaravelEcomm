<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Message;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Message\Actions\ShowMessageAction;
use Modules\Message\Models\Message;
use Modules\Message\Repository\MessageRepository;
use Tests\Unit\Actions\ActionTestCase;

class ShowMessageActionTest extends ActionTestCase
{
    public function test_execute_shows_message_by_id(): void
    {
        // Arrange
        $message = Message::factory()->create([
            'name' => 'Test User',
            'subject' => 'Test Subject',
            'email' => 'test@example.com',
            'message' => 'Test message content.',
        ]);

        $repository = new MessageRepository();
        $action = new ShowMessageAction($repository);

        // Act
        $result = $action->execute($message->id);

        // Assert
        $this->assertInstanceOf(Model::class, $result);
        $this->assertInstanceOf(Message::class, $result);
        $this->assertEquals($message->id, $result->id);
        $this->assertEquals('Test User', $result->name);
        $this->assertEquals('Test Subject', $result->subject);
        $this->assertEquals('test@example.com', $result->email);
        $this->assertEquals('Test message content.', $result->message);
    }

    public function test_execute_finds_message_with_all_attributes(): void
    {
        // Arrange
        $message = Message::factory()->create([
            'name' => 'Complete User',
            'subject' => 'Complete Subject',
            'email' => 'complete@example.com',
            'phone' => '+1234567890',
            'message' => 'Complete message content.',
            'is_read' => true,
        ]);

        $repository = new MessageRepository();
        $action = new ShowMessageAction($repository);

        // Act
        $result = $action->execute($message->id);

        // Assert
        $this->assertEquals('Complete User', $result->name);
        $this->assertEquals('Complete Subject', $result->subject);
        $this->assertEquals('complete@example.com', $result->email);
        $this->assertEquals('+1234567890', $result->phone);
        $this->assertEquals('Complete message content.', $result->message);
        $this->assertTrue($result->is_read);
    }

    public function test_execute_throws_exception_for_nonexistent_message(): void
    {
        // Arrange
        $repository = new MessageRepository();
        $action = new ShowMessageAction($repository);

        // Assert & Act
        $this->expectException(ModelNotFoundException::class);
        $action->execute(999999);
    }

    public function test_execute_finds_message_with_parent(): void
    {
        // Arrange
        $parentMessage = Message::factory()->create();
        $childMessage = Message::factory()->create([
            'parent_id' => $parentMessage->id,
        ]);

        $repository = new MessageRepository();
        $action = new ShowMessageAction($repository);

        // Act
        $result = $action->execute($childMessage->id);

        // Assert
        $this->assertInstanceOf(Message::class, $result);
        $this->assertEquals($childMessage->id, $result->id);
        $this->assertEquals($parentMessage->id, $result->parent_id);
    }
}
