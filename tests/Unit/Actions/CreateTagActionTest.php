<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Tag\Actions\CreateTagAction;
use Modules\Tag\DTOs\TagDto;
use Modules\Tag\Models\Tag;
use Modules\Tag\Repository\TagRepository;
use Tests\TestCase;

class CreateTagActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_execute_creates_tag_with_dto(): void
    {
        // Arrange
        $repository = new TagRepository();
        $action = new CreateTagAction($repository);

        $dto = new TagDto(
            id: null,
            title: 'Test Tag',
            slug: 'test-tag',
            status: 'active'
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(Tag::class, $result);
        $this->assertEquals('Test Tag', $result->title);
        $this->assertEquals('test-tag', $result->slug);
        $this->assertEquals('active', $result->status);
        $this->assertDatabaseHas('tags', [
            'title' => 'Test Tag',
            'slug' => 'test-tag',
            'status' => 'active',
        ]);
    }

    public function test_execute_generates_slug_when_not_provided(): void
    {
        // Arrange
        $repository = new TagRepository();
        $action = new CreateTagAction($repository);

        $dto = TagDto::fromArray([
            'title' => 'Test Tag Without Slug',
            'status' => 'active',
        ]);

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(Tag::class, $result);
        $this->assertNotEmpty($result->slug);
        $this->assertEquals('test-tag-without-slug', $result->slug);
    }
}
