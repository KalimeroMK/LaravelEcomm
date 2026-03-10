<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Attribute;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Modules\Attribute\Actions\AttributeGroup\DeleteAttributeGroupAction;
use Modules\Attribute\Models\AttributeGroup;
use Modules\Attribute\Repository\AttributeGroupRepository;
use Tests\Unit\Actions\ActionTestCase;

class DeleteAttributeGroupActionTest extends ActionTestCase
{
    use RefreshDatabase;

    public function test_execute_deletes_attribute_group(): void
    {
        // Arrange
        $group = AttributeGroup::factory()->create([
            'name' => 'General Properties',
        ]);

        $repository = new AttributeGroupRepository();
        $action = new DeleteAttributeGroupAction($repository);

        $this->assertDatabaseHas('attribute_groups', ['id' => $group->id]);

        // Act
        $result = $action->execute($group->id);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertDatabaseMissing('attribute_groups', ['id' => $group->id]);
    }

    public function test_execute_returns_json_response(): void
    {
        // Arrange
        $group = AttributeGroup::factory()->create(['name' => 'Test Group']);

        $repository = new AttributeGroupRepository();
        $action = new DeleteAttributeGroupAction($repository);

        // Act
        $result = $action->execute($group->id);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(200, $result->getStatusCode());
    }

    public function test_execute_deletes_multiple_attribute_groups(): void
    {
        // Arrange
        $group1 = AttributeGroup::factory()->create(['name' => 'Group 1']);
        $group2 = AttributeGroup::factory()->create(['name' => 'Group 2']);
        $group3 = AttributeGroup::factory()->create(['name' => 'Group 3']);

        $repository = new AttributeGroupRepository();
        $action = new DeleteAttributeGroupAction($repository);

        $this->assertDatabaseCount('attribute_groups', 3);

        // Act
        $action->execute($group1->id);
        $action->execute($group2->id);

        // Assert
        $this->assertDatabaseMissing('attribute_groups', ['id' => $group1->id]);
        $this->assertDatabaseMissing('attribute_groups', ['id' => $group2->id]);
        $this->assertDatabaseHas('attribute_groups', ['id' => $group3->id]);
    }

    public function test_execute_does_not_throw_exception_for_non_existent_group(): void
    {
        // Arrange
        $repository = new AttributeGroupRepository();
        $action = new DeleteAttributeGroupAction($repository);

        // Act & Assert - The repository's destroy method may not throw exception for non-existent ID
        $this->expectNotToPerformAssertions();
        $action->execute(99999);
    }

    public function test_execute_deletes_group_with_attributes(): void
    {
        // Arrange
        $group = AttributeGroup::factory()->create(['name' => 'Features']);
        $group->attributes()->attach(\Modules\Attribute\Models\Attribute::factory()->create(['name' => 'Color']));

        $repository = new AttributeGroupRepository();
        $action = new DeleteAttributeGroupAction($repository);

        $this->assertDatabaseHas('attribute_groups', ['id' => $group->id]);
        $this->assertDatabaseHas('attribute_attribute_group', ['attribute_group_id' => $group->id]);

        // Act
        $action->execute($group->id);

        // Assert
        $this->assertDatabaseMissing('attribute_groups', ['id' => $group->id]);
        // The pivot table entries should also be cleaned up
        $this->assertDatabaseMissing('attribute_attribute_group', ['attribute_group_id' => $group->id]);
    }
}
