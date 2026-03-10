<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Attribute;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Modules\Attribute\Actions\AttributeGroup\GetAllAttributeGroupsAction;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Models\AttributeGroup;
use Modules\Attribute\Repository\AttributeGroupRepository;
use Tests\Unit\Actions\ActionTestCase;

class GetAllAttributeGroupsActionTest extends ActionTestCase
{
    use RefreshDatabase;

    public function test_execute_returns_empty_collection_when_no_groups(): void
    {
        // Arrange
        $repository = new AttributeGroupRepository();
        $action = new GetAllAttributeGroupsAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEmpty($result);
    }

    public function test_execute_returns_all_attribute_groups(): void
    {
        // Arrange
        AttributeGroup::factory()->create(['name' => 'General']);
        AttributeGroup::factory()->create(['name' => 'Technical']);
        AttributeGroup::factory()->create(['name' => 'Physical']);

        $repository = new AttributeGroupRepository();
        $action = new GetAllAttributeGroupsAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(3, $result);
        $this->assertContainsOnlyInstancesOf(AttributeGroup::class, $result);
    }

    public function test_execute_returns_groups_with_attributes(): void
    {
        // Arrange
        $group = AttributeGroup::factory()->create(['name' => 'Features']);
        $attribute1 = Attribute::factory()->create(['name' => 'Color']);
        $attribute2 = Attribute::factory()->create(['name' => 'Size']);
        $group->attributes()->attach([$attribute1->id, $attribute2->id]);

        $repository = new AttributeGroupRepository();
        $action = new GetAllAttributeGroupsAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertCount(1, $result);
        $firstGroup = $result->first();
        $this->assertEquals('Features', $firstGroup->name);
    }

    public function test_execute_returns_collection_with_correct_names(): void
    {
        // Arrange
        $names = ['Group A', 'Group B', 'Group C'];
        foreach ($names as $name) {
            AttributeGroup::factory()->create(['name' => $name]);
        }

        $repository = new AttributeGroupRepository();
        $action = new GetAllAttributeGroupsAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertCount(3, $result);
        $resultNames = $result->pluck('name')->toArray();
        foreach ($names as $name) {
            $this->assertContains($name, $resultNames);
        }
    }

    public function test_execute_returns_empty_collection_after_deleting_all(): void
    {
        // Arrange
        $group1 = AttributeGroup::factory()->create();
        $group2 = AttributeGroup::factory()->create();

        $repository = new AttributeGroupRepository();
        $action = new GetAllAttributeGroupsAction($repository);

        $this->assertCount(2, $action->execute());

        // Act
        $group1->delete();
        $group2->delete();

        // Assert
        $result = $action->execute();
        $this->assertEmpty($result);
    }
}
