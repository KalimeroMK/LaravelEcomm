<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Message;

use Modules\Message\Actions\CreateMessageAction;
use Modules\Message\DTOs\MessageDTO;
use Modules\Message\Models\Message;
use Modules\Message\Repository\MessageRepository;
use Tests\Unit\Actions\ActionTestCase;

class CreateMessageActionTest extends ActionTestCase
{
    public function test_execute_creates_message_with_dto(): void
    {
        // Arrange
        $repository = new MessageRepository();
        $action = new CreateMessageAction($repository);

        $dto = new MessageDTO(
            id: null,
            name: 'John Doe',
            subject: 'Test Subject',
            email: 'john@example.com',
            phone: '+1234567890',
            message: 'This is a test message.',
            read_at: null,
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(Message::class, $result);
        $this->assertEquals('John Doe', $result->name);
        $this->assertEquals('Test Subject', $result->subject);
        $this->assertEquals('john@example.com', $result->email);
        $this->assertEquals('+1234567890', $result->phone);
        $this->assertEquals('This is a test message.', $result->message);
        $this->assertDatabaseHas('messages', [
            'name' => 'John Doe',
            'subject' => 'Test Subject',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'message' => 'This is a test message.',
        ]);
    }

    public function test_execute_creates_message_with_minimum_data(): void
    {
        // Arrange
        $repository = new MessageRepository();
        $action = new CreateMessageAction($repository);

        $dto = new MessageDTO(
            id: null,
            name: 'Jane Doe',
            subject: 'Another Subject',
            email: 'jane@example.com',
            phone: null,
            message: 'Another message.',
            read_at: null,
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(Message::class, $result);
        $this->assertEquals('Jane Doe', $result->name);
        $this->assertNull($result->phone);
    }

    public function test_execute_creates_message_with_read_at(): void
    {
        // Arrange
        $repository = new MessageRepository();
        $action = new CreateMessageAction($repository);
        $readAt = now()->toDateTimeString();

        $dto = new MessageDTO(
            id: null,
            name: 'Read Message',
            subject: 'Already Read',
            email: 'read@example.com',
            phone: null,
            message: 'This message was already read.',
            read_at: $readAt,
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(Message::class, $result);
        $this->assertNotNull($result->read_at);
    }

    public function test_execute_creates_multiple_messages(): void
    {
        // Arrange
        $repository = new MessageRepository();
        $action = new CreateMessageAction($repository);

        // Act
        $message1 = $action->execute(new MessageDTO(
            id: null,
            name: 'User One',
            subject: 'First Message',
            email: 'user1@example.com',
            phone: null,
            message: 'First message content.',
            read_at: null,
        ));

        $message2 = $action->execute(new MessageDTO(
            id: null,
            name: 'User Two',
            subject: 'Second Message',
            email: 'user2@example.com',
            phone: null,
            message: 'Second message content.',
            read_at: null,
        ));

        // Assert
        $this->assertInstanceOf(Message::class, $message1);
        $this->assertInstanceOf(Message::class, $message2);
        $this->assertEquals(2, Message::count());
    }
}
