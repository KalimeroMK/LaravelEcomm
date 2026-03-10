<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Tag;

use Modules\Tag\Actions\DeleteTagAction;
use Modules\Tag\Models\Tag;
use Modules\Tag\Repository\TagRepository;
use Tests\Unit\Actions\ActionTestCase;

class DeleteTagActionTest extends ActionTestCase
{
    public function test_execute_deletes_existing_tag(): void
    {
        // Arrange
        $tag = Tag::factory()->create([
            'title' => 'Tag to Delete',
            'slug' => 'tag-to-delete',
            'status' => 'active',
        ]);

        $repository = new TagRepository();
        $action = new DeleteTagAction($repository);

        // Act
        $action->execute($tag->id);

        // Assert
        $this->assertDatabaseMissing('tags', [
            'id' => $tag->id,
            'title' => 'Tag to Delete',
        ]);
    }

    public function test_execute_deletes_tag_and_verifies_count(): void
    {
        // Arrange
        $tag1 = Tag::factory()->create();
        $tag2 = Tag::factory()->create();
        $tag3 = Tag::factory()->create();

        $repository = new TagRepository();
        $action = new DeleteTagAction($repository);

        // Act
        $action->execute($tag2->id);

        // Assert
        $this->assertDatabaseHas('tags', ['id' => $tag1->id]);
        $this->assertDatabaseMissing('tags', ['id' => $tag2->id]);
        $this->assertDatabaseHas('tags', ['id' => $tag3->id]);
        $this->assertEquals(2, Tag::count());
    }

    public function test_execute_deletes_tag_with_relations(): void
    {
        // Arrange
        $tag = Tag::factory()->create([
            'title' => 'Tag with Relations',
            'slug' => 'tag-with-relations',
        ]);
        $product = \Modules\Product\Models\Product::factory()->create();
        $tag->product()->attach($product);

        $repository = new TagRepository();
        $action = new DeleteTagAction($repository);

        // Act
        $action->execute($tag->id);

        // Assert
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
        $this->assertDatabaseMissing('product_tag', ['tag_id' => $tag->id]);
        // Product itself should still exist
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }
}
