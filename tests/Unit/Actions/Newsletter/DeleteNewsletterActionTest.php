<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Newsletter;

use Modules\Newsletter\Actions\DeleteNewsletterAction;
use Modules\Newsletter\Models\Newsletter;
use Modules\Newsletter\Repository\NewsletterRepository;
use Tests\Unit\Actions\ActionTestCase;

class DeleteNewsletterActionTest extends ActionTestCase
{
    public function test_execute_deletes_existing_newsletter(): void
    {
        // Arrange
        $newsletter = Newsletter::factory()->create([
            'email' => 'delete@example.com',
        ]);

        $repository = new NewsletterRepository();
        $action = new DeleteNewsletterAction($repository);

        // Act
        $action->execute($newsletter->id);

        // Assert
        $this->assertDatabaseMissing('newsletters', [
            'id' => $newsletter->id,
            'email' => 'delete@example.com',
        ]);
    }

    public function test_execute_deletes_newsletter_and_verifies_count(): void
    {
        // Arrange
        $newsletter1 = Newsletter::factory()->create();
        $newsletter2 = Newsletter::factory()->create();
        $newsletter3 = Newsletter::factory()->create();

        $repository = new NewsletterRepository();
        $action = new DeleteNewsletterAction($repository);

        // Act
        $action->execute($newsletter2->id);

        // Assert
        $this->assertDatabaseHas('newsletters', ['id' => $newsletter1->id]);
        $this->assertDatabaseMissing('newsletters', ['id' => $newsletter2->id]);
        $this->assertDatabaseHas('newsletters', ['id' => $newsletter3->id]);
        $this->assertEquals(2, Newsletter::count());
    }

    public function test_execute_deletes_validated_and_unvalidated_newsletters(): void
    {
        // Arrange
        $validatedNewsletter = Newsletter::factory()->create([
            'email' => 'validated@example.com',
            'is_validated' => true,
        ]);
        $unvalidatedNewsletter = Newsletter::factory()->create([
            'email' => 'unvalidated@example.com',
            'is_validated' => false,
        ]);

        $repository = new NewsletterRepository();
        $action = new DeleteNewsletterAction($repository);

        // Act
        $action->execute($validatedNewsletter->id);

        // Assert
        $this->assertDatabaseMissing('newsletters', ['id' => $validatedNewsletter->id]);
        $this->assertDatabaseHas('newsletters', ['id' => $unvalidatedNewsletter->id]);
    }
}
