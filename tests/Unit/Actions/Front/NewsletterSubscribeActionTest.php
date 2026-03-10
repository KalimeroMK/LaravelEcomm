<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Front;

use Illuminate\Http\Request;
use Modules\Front\Actions\NewsletterSubscribeAction;
use Modules\Newsletter\Models\Newsletter;
use Tests\Unit\Actions\ActionTestCase;

class NewsletterSubscribeActionTest extends ActionTestCase
{
    public function test_invoke_subscribes_new_email(): void
    {
        // Arrange
        $request = new Request(['email' => 'subscriber@example.com']);
        $action = app(NewsletterSubscribeAction::class);

        // Act
        $result = $action($request);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseHas('newsletters', [
            'email' => 'subscriber@example.com',
        ]);
    }

    public function test_invoke_returns_false_for_existing_email(): void
    {
        // Arrange
        Newsletter::factory()->create(['email' => 'existing@example.com']);

        $request = new Request(['email' => 'existing@example.com']);
        $action = app(NewsletterSubscribeAction::class);

        // Act
        $result = $action($request);

        // Assert
        $this->assertFalse($result);
    }

    public function test_invoke_creates_newsletter_record(): void
    {
        // Arrange
        $request = new Request(['email' => 'new@example.com']);
        $action = app(NewsletterSubscribeAction::class);

        // Act
        $action($request);

        // Assert
        $this->assertDatabaseCount('newsletters', 1);
    }

    public function test_invoke_does_not_duplicate_emails(): void
    {
        // Arrange
        Newsletter::factory()->create(['email' => 'test@example.com']);

        $request = new Request(['email' => 'test@example.com']);
        $action = app(NewsletterSubscribeAction::class);

        // Act
        $result = $action($request);

        // Assert
        $this->assertFalse($result);
        $this->assertDatabaseCount('newsletters', 1);
    }

    public function test_invoke_handles_different_emails(): void
    {
        // Arrange
        Newsletter::factory()->create(['email' => 'first@example.com']);

        $request = new Request(['email' => 'second@example.com']);
        $action = app(NewsletterSubscribeAction::class);

        // Act
        $result = $action($request);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseCount('newsletters', 2);
    }
}
