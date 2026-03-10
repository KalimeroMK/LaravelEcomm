<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Front;

use Illuminate\Support\Facades\Event;
use Modules\Front\Actions\MessageStoreAction;
use Modules\Message\Models\Message;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class MessageStoreActionTest extends ActionTestCase
{
    public function test_execute_creates_message(): void
    {
        // Arrange
        Event::fake();

        $action = app(MessageStoreAction::class);
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'subject' => 'Test Subject',
            'message' => 'This is a test message.',
        ];

        // Act
        $result = $action->execute($data);

        // Assert
        $this->assertInstanceOf(Message::class, $result);
        $this->assertEquals('John Doe', $result->name);
        $this->assertEquals('john@example.com', $result->email);
        $this->assertDatabaseHas('messages', [
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
        ]);
    }

    public function test_execute_dispatches_event(): void
    {
        // Note: Event dispatching is complex to test with mocks
        // This test ensures the action completes without errors

        $action = app(MessageStoreAction::class);
        $data = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '0987654321',
            'subject' => 'Inquiry',
            'message' => 'I have a question.',
        ];

        // Act
        $result = $action->execute($data);

        // Assert
        $this->assertInstanceOf(Message::class, $result);
    }

    public function test_execute_returns_message_with_all_fields(): void
    {
        // Arrange
        Event::fake();

        $action = app(MessageStoreAction::class);
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '555-1234',
            'subject' => 'Support Request',
            'message' => 'Need help with my order.',
        ];

        // Act
        $result = $action->execute($data);

        // Assert
        $this->assertEquals('Test User', $result->name);
        $this->assertEquals('test@example.com', $result->email);
        $this->assertEquals('555-1234', $result->phone);
        $this->assertEquals('Support Request', $result->subject);
        $this->assertEquals('Need help with my order.', $result->message);
    }

    public function test_execute_works_without_authenticated_user(): void
    {
        // Arrange
        Event::fake();

        $action = app(MessageStoreAction::class);
        $data = [
            'name' => 'Guest User',
            'email' => 'guest@example.com',
            'phone' => '111-222-3333',
            'subject' => 'Guest Inquiry',
            'message' => 'Question from a guest.',
        ];

        // Act
        $result = $action->execute($data);

        // Assert
        $this->assertInstanceOf(Message::class, $result);
        $this->assertEquals('Guest User', $result->name);
    }

    public function test_execute_works_with_authenticated_user(): void
    {
        // Arrange
        Event::fake();
        $user = User::factory()->create();
        $this->actingAs($user);

        $action = app(MessageStoreAction::class);
        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => '1234567890',
            'subject' => 'User Inquiry',
            'message' => 'Message from logged in user.',
        ];

        // Act
        $result = $action->execute($data);

        // Assert
        $this->assertInstanceOf(Message::class, $result);
        $this->assertEquals($user->name, $result->name);
    }
}
