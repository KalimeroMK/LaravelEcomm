<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Tag;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Tag\Actions\ShowTagAction;
use Modules\Tag\Models\Tag;
use Modules\Tag\Repository\TagRepository;
use Tests\Unit\Actions\ActionTestCase;

class ShowTagActionTest extends ActionTestCase
{
    public function test_execute_finds_tag_by_id(): void
    {
        // Arrange
        $tag = Tag::factory()->create([
            'title' => 'Findable Tag',
            'slug' => 'findable-tag',
            'status' => 'active',
        ]);

        $repository = new TagRepository();
        $action = new ShowTagAction($repository);

        // Act
        $result = $action->execute($tag->id);

        // Assert
        $this->assertInstanceOf(Model::class, $result);
        $this->assertInstanceOf(Tag::class, $result);
        $this->assertEquals($tag->id, $result->id);
        $this->assertEquals('Findable Tag', $result->title);
        $this->assertEquals('findable-tag', $result->slug);
    }

    public function test_execute_finds_tag_with_all_attributes(): void
    {
        // Arrange
        $tag = Tag::factory()->create([
            'title' => 'Complete Tag',
            'slug' => 'complete-tag',
            'status' => 'active',
        ]);

        $repository = new TagRepository();
        $action = new ShowTagAction($repository);

        // Act
        $result = $action->execute($tag->id);

        // Assert
        $this->assertEquals('Complete Tag', $result->title);
        $this->assertEquals('complete-tag', $result->slug);
        $this->assertEquals('active', $result->status);
    }

    public function test_execute_throws_exception_for_nonexistent_tag(): void
    {
        // Arrange
        $repository = new TagRepository();
        $action = new ShowTagAction($repository);

        // Assert & Act
        $this->expectException(ModelNotFoundException::class);
        $action->execute(999999);
    }

    public function test_execute_finds_tag_with_products(): void
    {
        // Arrange
        $tag = Tag::factory()->create([
            'title' => 'Tag with Products',
            'slug' => 'tag-with-products',
        ]);
        $product = \Modules\Product\Models\Product::factory()->create();
        $tag->product()->attach($product);

        $repository = new TagRepository();
        $action = new ShowTagAction($repository);

        // Act
        $result = $action->execute($tag->id);

        // Assert
        $this->assertInstanceOf(Tag::class, $result);
        $this->assertEquals($tag->id, $result->id);
        $this->assertEquals('Tag with Products', $result->title);
    }
}
