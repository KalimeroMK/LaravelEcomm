<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Attribute;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Attribute\Actions\AttributeGroup\CreateAttributeGroupAction;
use Modules\Attribute\DTOs\AttributeGroupDTO;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Models\AttributeGroup;
use Modules\Attribute\Repository\AttributeGroupRepository;
use Tests\Unit\Actions\ActionTestCase;

class CreateAttributeGroupActionTest extends ActionTestCase
{
    use RefreshDatabase;

    public function test_execute_creates_attribute_group_with_dto(): void
    {
        // Arrange
        $repository = new AttributeGroupRepository();
        $action = new CreateAttributeGroupAction($repository);

        $dto = new AttributeGroupDTO(
            id: null,
            name: 'General Properties',
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(AttributeGroup::class, $result);
        $this->assertEquals('General Properties', $result->name);
        $this->assertDatabaseHas('attribute_groups', [
            'name' => 'General Properties',
        ]);
    }

    public function test_execute_creates_attribute_group_with_attributes(): void
    {
        // Arrange
        $attribute1 = Attribute::factory()->create(['name' => 'Color']);
        $attribute2 = Attribute::factory()->create(['name' => 'Size']);

        $repository = new AttributeGroupRepository();
        $action = new CreateAttributeGroupAction($repository);

        $dto = new AttributeGroupDTO(
            id: null,
            name: 'Product Features',
            attributes: [$attribute1->id, $attribute2->id],
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(AttributeGroup::class, $result);
        $this->assertEquals('Product Features', $result->name);
        $this->assertCount(2, $result->attributes);
        $this->assertTrue($result->attributes->contains($attribute1));
        $this->assertTrue($result->attributes->contains($attribute2));
    }

    public function test_execute_creates_attribute_group_without_attributes_when_empty_array(): void
    {
        // Arrange
        $repository = new AttributeGroupRepository();
        $action = new CreateAttributeGroupAction($repository);

        $dto = new AttributeGroupDTO(
            id: null,
            name: 'Empty Group',
            attributes: [],
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(AttributeGroup::class, $result);
        $this->assertEquals('Empty Group', $result->name);
        $this->assertCount(0, $result->attributes);
    }

    public function test_execute_creates_attribute_group_without_attributes_when_null(): void
    {
        // Arrange
        $repository = new AttributeGroupRepository();
        $action = new CreateAttributeGroupAction($repository);

        $dto = new AttributeGroupDTO(
            id: null,
            name: 'No Attributes Group',
            attributes: null,
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(AttributeGroup::class, $result);
        $this->assertEquals('No Attributes Group', $result->name);
        $this->assertCount(0, $result->attributes);
    }

    public function test_execute_creates_multiple_attribute_groups(): void
    {
        // Arrange
        $repository = new AttributeGroupRepository();
        $action = new CreateAttributeGroupAction($repository);

        $groups = ['General', 'Technical', 'Physical'];

        foreach ($groups as $groupName) {
            $dto = new AttributeGroupDTO(
                id: null,
                name: $groupName,
            );
            $action->execute($dto);
        }

        // Assert
        $this->assertDatabaseCount('attribute_groups', 3);
        foreach ($groups as $groupName) {
            $this->assertDatabaseHas('attribute_groups', ['name' => $groupName]);
        }
    }
}
