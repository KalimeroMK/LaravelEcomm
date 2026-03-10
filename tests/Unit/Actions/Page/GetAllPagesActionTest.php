<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Page;

use Illuminate\Support\Collection;
use Modules\Page\Actions\GetAllPagesAction;
use Modules\Page\DTOs\PageListDTO;
use Modules\Page\Models\Page;
use Modules\Page\Repository\PageRepository;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class GetAllPagesActionTest extends ActionTestCase
{
    public function test_execute_returns_page_list_dto(): void
    {
        // Arrange
        $user = User::factory()->create();
        Page::factory()->create(['title' => 'Page 1', 'user_id' => $user->id]);
        Page::factory()->create(['title' => 'Page 2', 'user_id' => $user->id]);
        Page::factory()->create(['title' => 'Page 3', 'user_id' => $user->id]);

        $repository = new PageRepository();
        $action = new GetAllPagesAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(PageListDTO::class, $result);
        $this->assertInstanceOf(Collection::class, $result->pages);
        $this->assertCount(3, $result->pages);
    }

    public function test_execute_returns_empty_collection_when_no_pages(): void
    {
        // Arrange
        $repository = new PageRepository();
        $action = new GetAllPagesAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(PageListDTO::class, $result);
        $this->assertInstanceOf(Collection::class, $result->pages);
        $this->assertCount(0, $result->pages);
        $this->assertTrue($result->pages->isEmpty());
    }

    public function test_execute_returns_collection_of_page_models(): void
    {
        // Arrange
        $user = User::factory()->create();
        Page::factory()->create(['title' => 'First Page', 'slug' => 'first-page', 'user_id' => $user->id]);
        Page::factory()->create(['title' => 'Second Page', 'slug' => 'second-page', 'user_id' => $user->id]);

        $repository = new PageRepository();
        $action = new GetAllPagesAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(Page::class, $result->pages->first());
        $this->assertInstanceOf(Page::class, $result->pages->last());
        $this->assertEquals('First Page', $result->pages->first()->title);
        $this->assertEquals('Second Page', $result->pages->last()->title);
    }
}
