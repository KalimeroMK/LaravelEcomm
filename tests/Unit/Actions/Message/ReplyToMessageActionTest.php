<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Message;

use Modules\Message\Actions\ReplyToMessageAction;
use Modules\Message\Models\Message;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class ReplyToMessageActionTest extends ActionTestCase
{
    public function test_execute_creates_reply_to_message(): void
    {
        // Arrange
        $user = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);
        $this->actingAs($user);

        $originalMessage = Message::factory()->create([
            'subject' => 'Original Subject',
        ]);

        $action = new ReplyToMessageAction();

        // Act
        $result = $action->execute($originalMessage, [
            'message' => 'This is a reply message.',
        ]);

        // Assert
        $this->assertInstanceOf(Message::class, $result);
        $this->assertEquals('Admin User', $result->name);
        $this->assertEquals('admin@example.com', $result->email);
        $this->assertEquals('Re: Original Subject', $result->subject);
        $this->assertEquals('This is a reply message.', $result->message);
        $this->assertEquals($originalMessage->id, $result->parent_id);
        $this->assertTrue($result->is_read);
        $this->assertDatabaseHas('messages', [
            'parent_id' => $originalMessage->id,
            'subject' => 'Re: Original Subject',
            'message' => 'This is a reply message.',
        ]);
    }

    public function test_execute_creates_reply_with_different_content(): void
    {
        // Arrange
        $user = User::factory()->create([
            'name' => 'Support Agent',
            'email' => 'support@example.com',
        ]);
        $this->actingAs($user);

        $originalMessage = Message::factory()->create([
            'subject' => 'Help Needed',
        ]);

        $action = new ReplyToMessageAction();

        // Act
        $result = $action->execute($originalMessage, [
            'message' => 'Thank you for contacting us. We will help you shortly.',
        ]);

        // Assert
        $this->assertEquals('Support Agent', $result->name);
        $this->assertEquals('Re: Help Needed', $result->subject);
        $this->assertEquals('Thank you for contacting us. We will help you shortly.', $result->message);
    }

    public function test_execute_sets_is_read_to_true(): void
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);

        $originalMessage = Message::factory()->create();

        $action = new ReplyToMessageAction();

        // Act
        $result = $action->execute($originalMessage, [
            'message' => 'Reply content.',
        ]);

        // Assert
        $this->assertTrue($result->is_read);
        $this->assertDatabaseHas('messages', [
            'id' => $result->id,
            'is_read' => true,
        ]);
    }

    public function test_execute_creates_multiple_replies(): void
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);

        $originalMessage = Message::factory()->create([
            'subject' => 'Thread Subject',
        ]);

        $action = new ReplyToMessageAction();

        // Act
        $reply1 = $action->execute($originalMessage, ['message' => 'First reply.']);
        $reply2 = $action->execute($originalMessage, ['message' => 'Second reply.']);

        // Assert
        $this->assertEquals($originalMessage->id, $reply1->parent_id);
        $this->assertEquals($originalMessage->id, $reply2->parent_id);
        $this->assertEquals(3, Message::count()); // Original + 2 replies
    }

    public function test_execute_preserves_original_message(): void
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);

        $originalMessage = Message::factory()->create([
            'subject' => 'Original',
            'message' => 'Original message content.',
        ]);

        $action = new ReplyToMessageAction();

        // Act
        $reply = $action->execute($originalMessage, ['message' => 'Reply content.']);

        // Assert
        $originalMessage->refresh();
        $this->assertEquals('Original', $originalMessage->subject);
        $this->assertEquals('Original message content.', $originalMessage->message);
        $this->assertNotEquals($originalMessage->id, $reply->id);
    }
}
