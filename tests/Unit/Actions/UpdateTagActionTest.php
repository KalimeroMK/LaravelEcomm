<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Tag\Actions\UpdateTagAction;
use Modules\Tag\DTOs\TagDto;
use Modules\Tag\Models\Tag;
use Modules\Tag\Repository\TagRepository;
use Tests\TestCase;

class UpdateTagActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_execute_updates_tag_with_dto(): void
    {
        // Arrange
        $tag = Tag::factory()->create([
            'title' => 'Old Title',
            'slug' => 'old-slug',
            'status' => 'active',
        ]);

        $repository = new TagRepository();
        $action = new UpdateTagAction($repository);

        $dto = new TagDto(
            id: $tag->id,
            title: 'New Title',
            slug: 'new-slug',
            status: 'inactive'
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(Tag::class, $result);
        $this->assertEquals('New Title', $result->title);
        $this->assertEquals('new-slug', $result->slug);
        $this->assertEquals('inactive', $result->status);
        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'title' => 'New Title',
            'slug' => 'new-slug',
            'status' => 'inactive',
        ]);
    }
}
