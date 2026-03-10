<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Message;

use Illuminate\Database\Eloquent\Model;
use Modules\Message\Actions\UpdateMessageAction;
use Modules\Message\DTOs\MessageDTO;
use Modules\Message\Models\Message;
use Modules\Message\Repository\MessageRepository;
use Tests\Unit\Actions\ActionTestCase;

class UpdateMessageActionTest extends ActionTestCase
{
    public function test_execute_updates_message_with_dto(): void
    {
        // Arrange
        $message = Message::factory()->create([
            'name' => 'Original Name',
            'subject' => 'Original Subject',
            'email' => 'original@example.com',
            'phone' => '+1111111111',
            'message' => 'Original message content.',
        ]);

        $repository = new MessageRepository();
        $action = new UpdateMessageAction($repository);

        $dto = new MessageDTO(
            id: $message->id,
            name: 'Updated Name',
            subject: 'Updated Subject',
            email: 'updated@example.com',
            phone: '+2222222222',
            message: 'Updated message content.',
            read_at: null,
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(Model::class, $result);
        $this->assertEquals('Updated Name', $result->name);
        $this->assertEquals('Updated Subject', $result->subject);
        $this->assertEquals('updated@example.com', $result->email);
        $this->assertEquals('+2222222222', $result->phone);
        $this->assertEquals('Updated message content.', $result->message);
        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'name' => 'Updated Name',
            'subject' => 'Updated Subject',
            'email' => 'updated@example.com',
        ]);
    }

    public function test_execute_preserves_existing_values_when_null(): void
    {
        // Arrange
        $message = Message::factory()->create([
            'name' => 'Original Name',
            'subject' => 'Original Subject',
            'email' => 'original@example.com',
            'phone' => '+1111111111',
            'message' => 'Original message content.',
        ]);

        $repository = new MessageRepository();
        $action = new UpdateMessageAction($repository);

        $dto = new MessageDTO(
            id: $message->id,
            name: 'Updated Name',
            subject: null,
            email: null,
            phone: null,
            message: null,
            read_at: null,
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertEquals('Updated Name', $result->name);
        $this->assertEquals('Original Subject', $result->subject); // preserved
        $this->assertEquals('original@example.com', $result->email); // preserved
        $this->assertEquals('+1111111111', $result->phone); // preserved
        $this->assertEquals('Original message content.', $result->message); // preserved
    }

    public function test_execute_updates_partial_fields(): void
    {
        // Arrange
        $message = Message::factory()->create([
            'name' => 'John Doe',
            'subject' => 'Hello',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'message' => 'Original message.',
        ]);

        $repository = new MessageRepository();
        $action = new UpdateMessageAction($repository);

        $dto = new MessageDTO(
            id: $message->id,
            name: 'John Doe',
            subject: 'Updated Hello',
            email: 'john@example.com',
            phone: '+1234567890',
            message: 'Updated message.',
            read_at: null,
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertEquals('John Doe', $result->name); // unchanged
        $this->assertEquals('Updated Hello', $result->subject); // updated
        $this->assertEquals('john@example.com', $result->email); // unchanged
        $this->assertEquals('Updated message.', $result->message); // updated
    }

    public function test_execute_throws_exception_for_nonexistent_message(): void
    {
        // Arrange
        $repository = new MessageRepository();
        $action = new UpdateMessageAction($repository);

        $dto = new MessageDTO(
            id: 999999,
            name: 'Nonexistent',
            subject: 'Nonexistent Subject',
            email: 'nonexistent@example.com',
            phone: null,
            message: 'This message does not exist.',
            read_at: null,
        );

        // Assert & Act
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $action->execute($dto);
    }

    public function test_execute_updates_message_using_from_array(): void
    {
        // Arrange
        $message = Message::factory()->create([
            'name' => 'Original',
            'subject' => 'Original Subject',
        ]);

        $repository = new MessageRepository();
        $action = new UpdateMessageAction($repository);

        $dto = MessageDTO::fromArray([
            'id' => $message->id,
            'name' => 'Updated from Array',
            'subject' => 'Updated Array Subject',
        ]);

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertEquals('Updated from Array', $result->name);
        $this->assertEquals('Updated Array Subject', $result->subject);
    }
}
