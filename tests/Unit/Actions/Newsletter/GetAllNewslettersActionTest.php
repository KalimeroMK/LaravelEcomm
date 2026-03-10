<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Newsletter;

use Illuminate\Support\Collection;
use Modules\Newsletter\Actions\GetAllNewslettersAction;
use Modules\Newsletter\Models\Newsletter;
use Modules\Newsletter\Repository\NewsletterRepository;
use Tests\Unit\Actions\ActionTestCase;

class GetAllNewslettersActionTest extends ActionTestCase
{
    public function test_execute_returns_collection(): void
    {
        // Arrange
        $repository = new NewsletterRepository();
        $action = new GetAllNewslettersAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
    }

    public function test_execute_returns_empty_collection_when_no_newsletters(): void
    {
        // Arrange
        $repository = new NewsletterRepository();
        $action = new GetAllNewslettersAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertTrue($result->isEmpty());
        $this->assertCount(0, $result);
    }

    public function test_execute_returns_all_newsletters(): void
    {
        // Arrange
        Newsletter::factory()->count(3)->create();

        $repository = new NewsletterRepository();
        $action = new GetAllNewslettersAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(3, $result);
    }

    public function test_execute_returns_newsletter_instances(): void
    {
        // Arrange
        Newsletter::factory()->create(['email' => 'one@example.com']);
        Newsletter::factory()->create(['email' => 'two@example.com']);

        $repository = new NewsletterRepository();
        $action = new GetAllNewslettersAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
        $this->assertInstanceOf(Newsletter::class, $result->first());
        $this->assertInstanceOf(Newsletter::class, $result->last());
    }

    public function test_execute_returns_newsletters_with_correct_attributes(): void
    {
        // Arrange
        Newsletter::factory()->create([
            'email' => 'validated@example.com',
            'is_validated' => true,
        ]);
        Newsletter::factory()->create([
            'email' => 'unvalidated@example.com',
            'is_validated' => false,
        ]);

        $repository = new NewsletterRepository();
        $action = new GetAllNewslettersAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertCount(2, $result);
        $emails = $result->pluck('email')->toArray();
        $this->assertContains('validated@example.com', $emails);
        $this->assertContains('unvalidated@example.com', $emails);
    }

    public function test_execute_returns_newsletters_ordered_by_id_desc(): void
    {
        // Arrange
        $newsletter1 = Newsletter::factory()->create(['email' => 'first@example.com']);
        $newsletter2 = Newsletter::factory()->create(['email' => 'second@example.com']);
        $newsletter3 = Newsletter::factory()->create(['email' => 'third@example.com']);

        $repository = new NewsletterRepository();
        $action = new GetAllNewslettersAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $ids = $result->pluck('id')->toArray();
        $this->assertEquals([$newsletter3->id, $newsletter2->id, $newsletter1->id], $ids);
    }

    public function test_execute_returns_collection_with_validated_status(): void
    {
        // Arrange
        Newsletter::factory()->create(['email' => 'valid1@example.com', 'is_validated' => true]);
        Newsletter::factory()->create(['email' => 'valid2@example.com', 'is_validated' => true]);
        Newsletter::factory()->create(['email' => 'invalid@example.com', 'is_validated' => false]);

        $repository = new NewsletterRepository();
        $action = new GetAllNewslettersAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertCount(3, $result);
        $validated = $result->where('is_validated', true);
        $unvalidated = $result->where('is_validated', false);
        $this->assertCount(2, $validated);
        $this->assertCount(1, $unvalidated);
    }
}
