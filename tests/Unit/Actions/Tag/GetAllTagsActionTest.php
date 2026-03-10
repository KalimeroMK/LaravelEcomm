<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Tag;

use Illuminate\Support\Collection;
use Modules\Tag\Actions\GetAllTagsAction;
use Modules\Tag\Models\Tag;
use Modules\Tag\Repository\TagRepository;
use Tests\Unit\Actions\ActionTestCase;

class GetAllTagsActionTest extends ActionTestCase
{
    public function test_execute_returns_all_tags(): void
    {
        // Arrange
        Tag::factory()->create(['title' => 'Tag 1', 'status' => 'active']);
        Tag::factory()->create(['title' => 'Tag 2', 'status' => 'active']);
        Tag::factory()->create(['title' => 'Tag 3', 'status' => 'inactive']);

        $repository = new TagRepository();
        $action = new GetAllTagsAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(3, $result);
    }

    public function test_execute_returns_collection_of_tag_models(): void
    {
        // Arrange
        Tag::factory()->count(2)->create();

        $repository = new TagRepository();
        $action = new GetAllTagsAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertInstanceOf(Tag::class, $result->first());
    }

    public function test_execute_returns_empty_collection_when_no_tags(): void
    {
        // Arrange
        $repository = new TagRepository();
        $action = new GetAllTagsAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertCount(0, $result);
        $this->assertTrue($result->isEmpty());
    }

    public function test_execute_returns_tags_ordered_by_id_desc(): void
    {
        // Arrange
        $tag1 = Tag::factory()->create(['title' => 'First Tag']);
        $tag2 = Tag::factory()->create(['title' => 'Second Tag']);
        $tag3 = Tag::factory()->create(['title' => 'Third Tag']);

        $repository = new TagRepository();
        $action = new GetAllTagsAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertEquals($tag3->id, $result->first()->id);
        $this->assertEquals($tag1->id, $result->last()->id);
    }
}
