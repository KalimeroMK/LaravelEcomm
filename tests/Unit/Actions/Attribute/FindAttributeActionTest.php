<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Attribute;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Attribute\Actions\FindAttributeAction;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Repository\AttributeRepository;
use Tests\Unit\Actions\ActionTestCase;

class FindAttributeActionTest extends ActionTestCase
{
    use RefreshDatabase;

    public function test_execute_finds_attribute_by_id(): void
    {
        // Arrange
        $attribute = Attribute::factory()->create([
            'name' => 'Color',
            'code' => 'color',
            'type' => Attribute::TYPE_STRING,
        ]);

        $repository = new AttributeRepository();
        $action = new FindAttributeAction($repository);

        // Act
        $result = $action->execute($attribute->id);

        // Assert
        $this->assertInstanceOf(Attribute::class, $result);
        $this->assertEquals($attribute->id, $result->id);
        $this->assertEquals('Color', $result->name);
        $this->assertEquals('color', $result->code);
    }

    public function test_execute_finds_attribute_with_all_fields(): void
    {
        // Arrange
        $attribute = Attribute::factory()->create([
            'name' => 'Size',
            'code' => 'size',
            'type' => Attribute::TYPE_STRING,
            'display' => Attribute::DISPLAY_SELECT,
            'is_required' => true,
            'is_filterable' => true,
            'is_configurable' => false,
        ]);

        $repository = new AttributeRepository();
        $action = new FindAttributeAction($repository);

        // Act
        $result = $action->execute($attribute->id);

        // Assert
        $this->assertEquals($attribute->id, $result->id);
        $this->assertEquals('Size', $result->name);
        $this->assertEquals('size', $result->code);
        $this->assertEquals(Attribute::TYPE_STRING, $result->type);
        $this->assertEquals(Attribute::DISPLAY_SELECT, $result->display);
        $this->assertTrue((bool) $result->is_required);
        $this->assertTrue((bool) $result->is_filterable);
        $this->assertFalse((bool) $result->is_configurable);
    }

    public function test_execute_throws_exception_for_non_existent_id(): void
    {
        // Arrange
        $repository = new AttributeRepository();
        $action = new FindAttributeAction($repository);

        // Act & Assert
        $this->expectException(\Exception::class);
        $action->execute(99999);
    }
}
