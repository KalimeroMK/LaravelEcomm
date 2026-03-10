<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Attribute;

use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Modules\Attribute\Actions\AttributeGroup\UpdateAttributeGroupAction;
use Modules\Attribute\DTOs\AttributeGroupDTO;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Models\AttributeGroup;
use Modules\Attribute\Repository\AttributeGroupRepository;
use Tests\Unit\Actions\ActionTestCase;

class UpdateAttributeGroupActionTest extends ActionTestCase
{
    use RefreshDatabase;

    public function test_execute_updates_attribute_group_with_dto(): void
    {
        // Arrange
        $group = AttributeGroup::factory()->create([
            'name' => 'Old Group Name',
        ]);

        $repository = new AttributeGroupRepository();
        $action = new UpdateAttributeGroupAction($repository);

        $dto = new AttributeGroupDTO(
            id: $group->id,
            name: 'New Group Name',
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(AttributeGroup::class, $result);
        $this->assertEquals('New Group Name', $result->name);
        $this->assertDatabaseHas('attribute_groups', [
            'id' => $group->id,
            'name' => 'New Group Name',
        ]);
        $this->assertDatabaseMissing('attribute_groups', [
            'id' => $group->id,
            'name' => 'Old Group Name',
        ]);
    }

    public function test_execute_updates_group_with_attributes(): void
    {
        // Arrange
        $group = AttributeGroup::factory()->create(['name' => 'Features']);
        $oldAttribute = Attribute::factory()->create(['name' => 'Old Attribute']);
        $group->attributes()->attach($oldAttribute);

        $newAttribute1 = Attribute::factory()->create(['name' => 'Color']);
        $newAttribute2 = Attribute::factory()->create(['name' => 'Size']);

        $repository = new AttributeGroupRepository();
        $action = new UpdateAttributeGroupAction($repository);

        $dto = new AttributeGroupDTO(
            id: $group->id,
            name: 'Product Features',
            attributes: [$newAttribute1->id, $newAttribute2->id],
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertEquals('Product Features', $result->name);
        $this->assertCount(2, $result->attributes);
        $this->assertTrue($result->attributes->contains($newAttribute1));
        $this->assertTrue($result->attributes->contains($newAttribute2));
        $this->assertFalse($result->attributes->contains($oldAttribute));
    }

    public function test_execute_syncs_empty_attributes_when_null(): void
    {
        // Arrange
        $group = AttributeGroup::factory()->create(['name' => 'Features']);
        $attribute = Attribute::factory()->create(['name' => 'Color']);
        $group->attributes()->attach($attribute);

        $repository = new AttributeGroupRepository();
        $action = new UpdateAttributeGroupAction($repository);

        $dto = new AttributeGroupDTO(
            id: $group->id,
            name: 'Empty Features',
            attributes: null,
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertEquals('Empty Features', $result->name);
        $this->assertCount(0, $result->fresh()->attributes);
    }

    public function test_execute_syncs_empty_attributes_when_empty_array(): void
    {
        // Arrange
        $group = AttributeGroup::factory()->create(['name' => 'Features']);
        $attribute = Attribute::factory()->create(['name' => 'Color']);
        $group->attributes()->attach($attribute);

        $repository = new AttributeGroupRepository();
        $action = new UpdateAttributeGroupAction($repository);

        $dto = new AttributeGroupDTO(
            id: $group->id,
            name: 'Cleared Features',
            attributes: [],
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertEquals('Cleared Features', $result->name);
        $this->assertCount(0, $result->fresh()->attributes);
    }

    public function test_execute_throws_exception_for_non_existent_group(): void
    {
        // Arrange
        $repository = new AttributeGroupRepository();
        $action = new UpdateAttributeGroupAction($repository);

        $dto = new AttributeGroupDTO(
            id: 99999,
            name: 'Non Existent',
        );

        // Act & Assert
        // The repository throws ModelNotFoundException before the action's check
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $action->execute($dto);
    }

    public function test_execute_throws_exception_when_id_is_null(): void
    {
        // Arrange
        $repository = new AttributeGroupRepository();
        $action = new UpdateAttributeGroupAction($repository);

        $dto = new AttributeGroupDTO(
            id: null,
            name: 'No ID Group',
        );

        // Act & Assert
        // The repository throws ModelNotFoundException when id is 0
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $action->execute($dto);
    }

    public function test_execute_updates_only_name_preserving_other_attributes(): void
    {
        // Arrange
        $group = AttributeGroup::factory()->create([
            'name' => 'Original Name',
        ]);

        $repository = new AttributeGroupRepository();
        $action = new UpdateAttributeGroupAction($repository);

        $dto = new AttributeGroupDTO(
            id: $group->id,
            name: 'Updated Name',
            attributes: [],
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertEquals('Updated Name', $result->name);
        $this->assertEquals($group->id, $result->id);
    }
}
