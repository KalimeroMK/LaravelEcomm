<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Message;

use Illuminate\Support\Collection;
use Modules\Message\Actions\GetAllMessagesAction;
use Modules\Message\Models\Message;
use Modules\Message\Repository\MessageRepository;
use Tests\Unit\Actions\ActionTestCase;

class GetAllMessagesActionTest extends ActionTestCase
{
    public function test_execute_returns_collection(): void
    {
        // Arrange
        $repository = new MessageRepository();
        $action = new GetAllMessagesAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
    }

    public function test_execute_returns_empty_collection_when_no_messages(): void
    {
        // Arrange
        $repository = new MessageRepository();
        $action = new GetAllMessagesAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertTrue($result->isEmpty());
        $this->assertCount(0, $result);
    }

    public function test_execute_returns_all_messages(): void
    {
        // Arrange
        Message::factory()->count(3)->create();

        $repository = new MessageRepository();
        $action = new GetAllMessagesAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(3, $result);
    }

    public function test_execute_returns_message_instances(): void
    {
        // Arrange
        Message::factory()->create(['subject' => 'Message One']);
        Message::factory()->create(['subject' => 'Message Two']);

        $repository = new MessageRepository();
        $action = new GetAllMessagesAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
        $this->assertInstanceOf(Message::class, $result->first());
        $this->assertInstanceOf(Message::class, $result->last());
    }

    public function test_execute_returns_messages_ordered_by_id_desc(): void
    {
        // Arrange
        $message1 = Message::factory()->create(['created_at' => now()->subDay()]);
        $message2 = Message::factory()->create(['created_at' => now()]);
        $message3 = Message::factory()->create(['created_at' => now()->subDays(2)]);

        $repository = new MessageRepository();
        $action = new GetAllMessagesAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertCount(3, $result);
        // Repository orders by id desc, so most recent ID should be first
        $ids = $result->pluck('id')->toArray();
        $this->assertEquals([$message3->id, $message2->id, $message1->id], $ids);
    }

    public function test_execute_returns_messages_with_correct_attributes(): void
    {
        // Arrange
        Message::factory()->create([
            'name' => 'John Doe',
            'subject' => 'Hello',
            'email' => 'john@example.com',
            'is_read' => true,
        ]);
        Message::factory()->create([
            'name' => 'Jane Doe',
            'subject' => 'Hi there',
            'email' => 'jane@example.com',
            'is_read' => false,
        ]);

        $repository = new MessageRepository();
        $action = new GetAllMessagesAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertCount(2, $result);
        $subjects = $result->pluck('subject')->toArray();
        $this->assertContains('Hello', $subjects);
        $this->assertContains('Hi there', $subjects);
    }
}
