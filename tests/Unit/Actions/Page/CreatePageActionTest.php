<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Page;

use Modules\Page\Actions\CreatePageAction;
use Modules\Page\DTOs\PageDTO;
use Modules\Page\Models\Page;
use Modules\Page\Repository\PageRepository;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class CreatePageActionTest extends ActionTestCase
{
    public function test_execute_creates_page_with_dto(): void
    {
        // Arrange
        $user = User::factory()->create();
        $repository = new PageRepository();
        $action = new CreatePageAction($repository);

        $dto = new PageDTO(
            id: null,
            title: 'Test Page',
            slug: 'test-page',
            content: 'This is a test page content.',
            is_active: true,
            user_id: $user->id,
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(Page::class, $result);
        $this->assertEquals('Test Page', $result->title);
        $this->assertEquals('test-page', $result->slug);
        $this->assertEquals('This is a test page content.', $result->content);
        $this->assertTrue($result->is_active);
        $this->assertEquals($user->id, $result->user_id);
        $this->assertDatabaseHas('pages', [
            'title' => 'Test Page',
            'slug' => 'test-page',
            'content' => 'This is a test page content.',
            'is_active' => true,
            'user_id' => $user->id,
        ]);
    }

    public function test_execute_creates_inactive_page(): void
    {
        // Arrange
        $user = User::factory()->create();
        $repository = new PageRepository();
        $action = new CreatePageAction($repository);

        $dto = new PageDTO(
            id: null,
            title: 'Inactive Page',
            slug: 'inactive-page',
            content: 'This is an inactive page.',
            is_active: false,
            user_id: $user->id,
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(Page::class, $result);
        $this->assertFalse($result->is_active);
        $this->assertDatabaseHas('pages', [
            'title' => 'Inactive Page',
            'is_active' => false,
        ]);
    }

    public function test_execute_creates_page_with_minimum_data(): void
    {
        // Arrange
        $user = User::factory()->create();
        $repository = new PageRepository();
        $action = new CreatePageAction($repository);

        $dto = PageDTO::fromArray([
            'title' => 'Minimal Page',
            'slug' => 'minimal-page',
            'content' => '',
            'user_id' => $user->id,
        ]);

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(Page::class, $result);
        $this->assertEquals('Minimal Page', $result->title);
        $this->assertEquals('', $result->content);
        $this->assertTrue($result->is_active); // default value
    }
}
