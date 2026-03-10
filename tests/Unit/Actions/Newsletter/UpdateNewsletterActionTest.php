<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Newsletter;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Newsletter\Actions\UpdateNewsletterAction;
use Modules\Newsletter\DTOs\NewsletterDTO;
use Modules\Newsletter\Models\Newsletter;
use Modules\Newsletter\Repository\NewsletterRepository;
use Tests\Unit\Actions\ActionTestCase;

class UpdateNewsletterActionTest extends ActionTestCase
{
    public function test_execute_updates_newsletter_with_dto(): void
    {
        // Arrange
        $newsletter = Newsletter::factory()->create([
            'email' => 'original@example.com',
            'token' => 'original-token',
            'is_validated' => false,
        ]);

        $repository = new NewsletterRepository();
        $action = new UpdateNewsletterAction($repository);

        $dto = new NewsletterDTO(
            id: $newsletter->id,
            email: 'updated@example.com',
            token: 'updated-token',
            is_validated: true,
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(Model::class, $result);
        $this->assertEquals('updated@example.com', $result->email);
        $this->assertEquals('updated-token', $result->token);
        $this->assertTrue($result->is_validated);
        $this->assertDatabaseHas('newsletters', [
            'id' => $newsletter->id,
            'email' => 'updated@example.com',
            'token' => 'updated-token',
            'is_validated' => true,
        ]);
    }

    public function test_execute_updates_only_email(): void
    {
        // Arrange
        $newsletter = Newsletter::factory()->create([
            'email' => 'old@example.com',
            'token' => 'token',
            'is_validated' => false,
        ]);

        $repository = new NewsletterRepository();
        $action = new UpdateNewsletterAction($repository);

        $dto = new NewsletterDTO(
            id: $newsletter->id,
            email: 'new@example.com',
            token: 'token',
            is_validated: false,
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertEquals('new@example.com', $result->email);
        $this->assertEquals('token', $result->token);
        $this->assertFalse($result->is_validated);
    }

    public function test_execute_updates_validation_status(): void
    {
        // Arrange
        $newsletter = Newsletter::factory()->create([
            'email' => 'test@example.com',
            'is_validated' => false,
        ]);

        $repository = new NewsletterRepository();
        $action = new UpdateNewsletterAction($repository);

        $dto = new NewsletterDTO(
            id: $newsletter->id,
            email: 'test@example.com',
            token: $newsletter->token,
            is_validated: true,
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertTrue($result->is_validated);
        $this->assertDatabaseHas('newsletters', [
            'id' => $newsletter->id,
            'is_validated' => true,
        ]);
    }

    public function test_execute_updates_newsletter_from_array(): void
    {
        // Arrange
        $newsletter = Newsletter::factory()->create([
            'email' => 'before@example.com',
            'is_validated' => false,
        ]);

        $repository = new NewsletterRepository();
        $action = new UpdateNewsletterAction($repository);

        $dto = NewsletterDTO::fromArray([
            'id' => $newsletter->id,
            'email' => 'after@example.com',
            'token' => 'new-token',
            'is_validated' => true,
        ]);

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertEquals('after@example.com', $result->email);
        $this->assertEquals('new-token', $result->token);
        $this->assertTrue($result->is_validated);
    }

    public function test_execute_throws_exception_for_nonexistent_newsletter(): void
    {
        // Arrange
        $repository = new NewsletterRepository();
        $action = new UpdateNewsletterAction($repository);

        $dto = new NewsletterDTO(
            id: 999999,
            email: 'nonexistent@example.com',
            token: 'token',
            is_validated: true,
        );

        // Assert & Act
        $this->expectException(ModelNotFoundException::class);
        $action->execute($dto);
    }

    public function test_execute_can_unvalidate_newsletter(): void
    {
        // Arrange
        $newsletter = Newsletter::factory()->create([
            'email' => 'validated@example.com',
            'is_validated' => true,
        ]);

        $repository = new NewsletterRepository();
        $action = new UpdateNewsletterAction($repository);

        $dto = new NewsletterDTO(
            id: $newsletter->id,
            email: 'validated@example.com',
            token: $newsletter->token,
            is_validated: false,
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertFalse($result->is_validated);
        $this->assertDatabaseHas('newsletters', [
            'id' => $newsletter->id,
            'is_validated' => false,
        ]);
    }
}
