<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Page;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Page\Actions\FindPageAction;
use Modules\Page\Models\Page;
use Modules\Page\Repository\PageRepository;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class FindPageActionTest extends ActionTestCase
{
    public function test_execute_finds_page_by_id(): void
    {
        // Arrange
        $user = User::factory()->create();
        $page = Page::factory()->create([
            'title' => 'Findable Page',
            'slug' => 'findable-page',
            'content' => 'Content to find',
            'user_id' => $user->id,
        ]);

        $repository = new PageRepository();
        $action = new FindPageAction($repository);

        // Act
        $result = $action->execute($page->id);

        // Assert
        $this->assertInstanceOf(Model::class, $result);
        $this->assertInstanceOf(Page::class, $result);
        $this->assertEquals($page->id, $result->id);
        $this->assertEquals('Findable Page', $result->title);
        $this->assertEquals('findable-page', $result->slug);
    }

    public function test_execute_finds_page_with_all_attributes(): void
    {
        // Arrange
        $user = User::factory()->create();
        $page = Page::factory()->create([
            'title' => 'Complete Page',
            'slug' => 'complete-page',
            'content' => 'Complete content here',
            'is_active' => true,
            'user_id' => $user->id,
        ]);

        $repository = new PageRepository();
        $action = new FindPageAction($repository);

        // Act
        $result = $action->execute($page->id);

        // Assert
        $this->assertEquals('Complete Page', $result->title);
        $this->assertEquals('complete-page', $result->slug);
        $this->assertEquals('Complete content here', $result->content);
        $this->assertTrue($result->is_active);
    }

    public function test_execute_throws_exception_for_nonexistent_page(): void
    {
        // Arrange
        $repository = new PageRepository();
        $action = new FindPageAction($repository);

        // Assert & Act
        $this->expectException(ModelNotFoundException::class);
        $action->execute(999999);
    }
}
