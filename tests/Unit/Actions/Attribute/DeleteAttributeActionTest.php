<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Attribute;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Attribute\Actions\DeleteAttributeAction;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Repository\AttributeRepository;
use Tests\Unit\Actions\ActionTestCase;

class DeleteAttributeActionTest extends ActionTestCase
{
    use RefreshDatabase;

    public function test_execute_deletes_attribute(): void
    {
        // Arrange
        $attribute = Attribute::factory()->create([
            'name' => 'Color',
            'code' => 'color',
        ]);

        $repository = new AttributeRepository();
        $action = new DeleteAttributeAction($repository);

        $this->assertDatabaseHas('attributes', ['id' => $attribute->id]);

        // Act
        $action->execute($attribute->id);

        // Assert
        $this->assertDatabaseMissing('attributes', ['id' => $attribute->id]);
    }

    public function test_execute_deletes_multiple_attributes(): void
    {
        // Arrange
        $attribute1 = Attribute::factory()->create(['name' => 'Color']);
        $attribute2 = Attribute::factory()->create(['name' => 'Size']);
        $attribute3 = Attribute::factory()->create(['name' => 'Weight']);

        $repository = new AttributeRepository();
        $action = new DeleteAttributeAction($repository);

        $this->assertDatabaseCount('attributes', 3);

        // Act
        $action->execute($attribute1->id);
        $action->execute($attribute2->id);

        // Assert
        $this->assertDatabaseMissing('attributes', ['id' => $attribute1->id]);
        $this->assertDatabaseMissing('attributes', ['id' => $attribute2->id]);
        $this->assertDatabaseHas('attributes', ['id' => $attribute3->id]);
    }

    public function test_execute_does_not_throw_exception_for_non_existent_attribute(): void
    {
        // Arrange
        $repository = new AttributeRepository();
        $action = new DeleteAttributeAction($repository);

        // Act & Assert - The repository's destroy method may not throw exception for non-existent ID
        $this->expectNotToPerformAssertions();
        $action->execute(99999);
    }
}
