<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Attribute;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Attribute\Actions\AttributeGroup\FindAttributeGroupAction;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Models\AttributeGroup;
use Modules\Attribute\Repository\AttributeGroupRepository;
use Tests\Unit\Actions\ActionTestCase;

class FindAttributeGroupActionTest extends ActionTestCase
{
    use RefreshDatabase;

    public function test_execute_finds_attribute_group_by_id(): void
    {
        // Arrange
        $group = AttributeGroup::factory()->create([
            'name' => 'General Properties',
        ]);

        $repository = new AttributeGroupRepository();
        $action = new FindAttributeGroupAction($repository);

        // Act
        $result = $action->execute($group->id);

        // Assert
        $this->assertInstanceOf(AttributeGroup::class, $result);
        $this->assertEquals($group->id, $result->id);
        $this->assertEquals('General Properties', $result->name);
    }

    public function test_execute_finds_attribute_group_with_attributes(): void
    {
        // Arrange
        $group = AttributeGroup::factory()->create(['name' => 'Features']);
        $attribute1 = Attribute::factory()->create(['name' => 'Color']);
        $attribute2 = Attribute::factory()->create(['name' => 'Size']);
        $group->attributes()->attach([$attribute1->id, $attribute2->id]);

        $repository = new AttributeGroupRepository();
        $action = new FindAttributeGroupAction($repository);

        // Act
        $result = $action->execute($group->id);

        // Assert
        $this->assertEquals($group->id, $result->id);
        $this->assertEquals('Features', $result->name);
    }

    public function test_execute_finds_different_attribute_groups(): void
    {
        // Arrange
        $group1 = AttributeGroup::factory()->create(['name' => 'General']);
        $group2 = AttributeGroup::factory()->create(['name' => 'Technical']);
        $group3 = AttributeGroup::factory()->create(['name' => 'Physical']);

        $repository = new AttributeGroupRepository();
        $action = new FindAttributeGroupAction($repository);

        // Act & Assert
        $result1 = $action->execute($group1->id);
        $this->assertEquals('General', $result1->name);

        $result2 = $action->execute($group2->id);
        $this->assertEquals('Technical', $result2->name);

        $result3 = $action->execute($group3->id);
        $this->assertEquals('Physical', $result3->name);
    }

    public function test_execute_throws_exception_for_non_existent_id(): void
    {
        // Arrange
        $repository = new AttributeGroupRepository();
        $action = new FindAttributeGroupAction($repository);

        // Act & Assert
        $this->expectException(\Exception::class);
        $action->execute(99999);
    }

    public function test_execute_returns_correct_instance_type(): void
    {
        // Arrange
        $group = AttributeGroup::factory()->create(['name' => 'Test Group']);

        $repository = new AttributeGroupRepository();
        $action = new FindAttributeGroupAction($repository);

        // Act
        $result = $action->execute($group->id);

        // Assert
        $this->assertInstanceOf(AttributeGroup::class, $result);
        $this->assertSame($group->id, $result->id);
    }
}
