<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Page;

use Modules\Page\Actions\DeletePageAction;
use Modules\Page\Models\Page;
use Modules\Page\Repository\PageRepository;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class DeletePageActionTest extends ActionTestCase
{
    public function test_execute_deletes_existing_page(): void
    {
        // Arrange
        $user = User::factory()->create();
        $page = Page::factory()->create([
            'title' => 'Page to Delete',
            'slug' => 'page-to-delete',
            'user_id' => $user->id,
        ]);

        $repository = new PageRepository();
        $action = new DeletePageAction($repository);

        // Act
        $action->execute($page->id);

        // Assert
        $this->assertDatabaseMissing('pages', [
            'id' => $page->id,
            'title' => 'Page to Delete',
        ]);
    }

    public function test_execute_deletes_page_and_verifies_count(): void
    {
        // Arrange
        $user = User::factory()->create();
        $page1 = Page::factory()->create(['user_id' => $user->id]);
        $page2 = Page::factory()->create(['user_id' => $user->id]);
        $page3 = Page::factory()->create(['user_id' => $user->id]);

        $repository = new PageRepository();
        $action = new DeletePageAction($repository);

        // Act
        $action->execute($page2->id);

        // Assert
        $this->assertDatabaseHas('pages', ['id' => $page1->id]);
        $this->assertDatabaseMissing('pages', ['id' => $page2->id]);
        $this->assertDatabaseHas('pages', ['id' => $page3->id]);
        $this->assertEquals(2, Page::count());
    }
}
