<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Post;

use Illuminate\Support\Collection;
use Modules\Post\Actions\GetAllTagsAction;
use Modules\Tag\Models\Tag;
use Tests\Unit\Actions\ActionTestCase;

class GetAllTagsActionTest extends ActionTestCase
{
    public function testExecuteReturnsCollection(): void
    {
        $action = app(GetAllTagsAction::class);
        $result = $action->execute();

        $this->assertInstanceOf(Collection::class, $result);
    }

    public function testExecuteReturnsAllTags(): void
    {
        Tag::factory()->count(5)->create();

        $action = app(GetAllTagsAction::class);
        $result = $action->execute();

        $this->assertCount(5, $result);
    }

    public function testExecuteReturnsEmptyCollectionWhenNoTags(): void
    {
        $action = app(GetAllTagsAction::class);
        $result = $action->execute();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(0, $result);
        $this->assertTrue($result->isEmpty());
    }

    public function testExecuteReturnsTagModels(): void
    {
        Tag::factory()->create(['title' => 'Test Tag']);

        $action = app(GetAllTagsAction::class);
        $result = $action->execute();

        $this->assertInstanceOf(Tag::class, $result->first());
        $this->assertEquals('Test Tag', $result->first()->title);
    }

    public function testExecuteReturnsTagsOrderedByIdDesc(): void
    {
        Tag::factory()->create(['title' => 'First Tag']);
        Tag::factory()->create(['title' => 'Second Tag']);
        Tag::factory()->create(['title' => 'Third Tag']);

        $action = app(GetAllTagsAction::class);
        $result = $action->execute();

        $this->assertCount(3, $result);
        // Tags should be ordered by id descending (newest first)
        $this->assertEquals('Third Tag', $result->first()->title);
        $this->assertEquals('First Tag', $result->last()->title);
    }

    public function testExecuteReturnsTagsWithCorrectData(): void
    {
        Tag::factory()->create([
            'title' => 'Laravel',
            'slug' => 'laravel',
            'status' => 'active',
        ]);
        Tag::factory()->create([
            'title' => 'PHP',
            'slug' => 'php',
            'status' => 'inactive',
        ]);

        $action = app(GetAllTagsAction::class);
        $result = $action->execute();

        $this->assertCount(2, $result);
        
        $titles = $result->pluck('title')->toArray();
        $this->assertContains('Laravel', $titles);
        $this->assertContains('PHP', $titles);
    }

    public function testExecuteReturnsTagsWithPostsRelation(): void
    {
        $tag = Tag::factory()->create(['title' => 'Tag With Posts']);
        \Modules\Post\Models\Post::factory()
            ->count(2)
            ->hasAttached($tag)
            ->create();

        $action = app(GetAllTagsAction::class);
        $result = $action->execute();

        $this->assertCount(1, $result);
        $this->assertInstanceOf(Tag::class, $result->first());
    }
}
