<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Message\Models\Message;
use Modules\User\Models\User;

require_once __DIR__ . '/../../../TestHelpers.php';

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = createAdminUser();
    $this->user = User::factory()->create();
});

test('user can send contact message', function () {
    $messageData = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'subject' => 'Test Message',
        'message' => 'This is a test message with at least twenty characters to pass validation',
        'phone' => '1234567890',
    ];

    $response = $this->post('/contact/message', $messageData);

    $response->assertRedirect();
    $this->assertDatabaseHas('messages', [
        'email' => 'john@example.com',
        'subject' => 'Test Message',
    ]);
});

test('admin can view messages list', function () {
    Message::factory()->count(5)->create();

    $response = $this->actingAs($this->admin, 'web')
        ->get('/admin/messages');

    $response->assertStatus(200);
    $response->assertSee('Messages');
});

test('admin can view message details', function () {
    $message = Message::factory()->create([
        'subject' => 'Test Subject',
        'message' => 'Test message content',
    ]);

    $response = $this->actingAs($this->admin, 'web')
        ->get("/admin/messages/{$message->id}");

    $response->assertStatus(200);
    $response->assertSee('Test Subject');
    $response->assertSee('Test message content');
});

test('admin can mark message as read', function () {
    $message = Message::factory()->create([
        'is_read' => false,
    ]);

    $response = $this->actingAs($this->admin, 'web')
        ->put("/admin/messages/{$message->id}/read");

    $response->assertRedirect();
    $this->assertDatabaseHas('messages', [
        'id' => $message->id,
        'is_read' => true,
    ]);
});

test('admin can reply to message', function () {
    $message = Message::factory()->create([
        'email' => 'test@example.com',
    ]);

    $replyData = [
        'subject' => 'Re: '.$message->subject,
        'message' => 'Thank you for your message. We will get back to you soon.',
    ];

    $response = $this->actingAs($this->admin, 'web')
        ->post("/admin/messages/{$message->id}/reply", $replyData);

    $response->assertRedirect();
    $this->assertDatabaseHas('messages', [
        'parent_id' => $message->id,
        'subject' => 'Re: '.$message->subject,
    ]);
});

test('admin can delete message', function () {
    $message = Message::factory()->create();

    $response = $this->actingAs($this->admin, 'web')
        ->delete("/admin/messages/{$message->id}");

    $response->assertRedirect();
    $this->assertDatabaseMissing('messages', [
        'id' => $message->id,
    ]);
});

test('admin can mark multiple messages as read', function () {
    $messages = Message::factory()->count(3)->create([
        'is_read' => false,
    ]);

    $messageIds = $messages->pluck('id')->toArray();

    $response = $this->actingAs($this->admin, 'web')
        ->post('/admin/messages/mark-read', [
            'message_ids' => $messageIds,
        ]);

    $response->assertRedirect();

    foreach ($messages as $message) {
        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'is_read' => true,
        ]);
    }
});

test('admin can filter messages by status', function () {
    Message::factory()->count(3)->create(['is_read' => true]);
    Message::factory()->count(2)->create(['is_read' => false]);

    $response = $this->actingAs($this->admin, 'web')
        ->get('/admin/messages?status=unread');

    $response->assertStatus(200);
    $response->assertSee('Unread Messages');
});

test('message validation works', function () {
    $response = $this->post('/contact/message', [
        'name' => '',
        'email' => 'invalid-email',
        'subject' => '',
        'message' => '',
    ]);

    $response->assertSessionHasErrors(['name', 'email', 'subject', 'message']);
});
