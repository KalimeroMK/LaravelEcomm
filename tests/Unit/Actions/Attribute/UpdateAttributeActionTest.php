<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Attribute;

use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Modules\Attribute\Actions\UpdateAttributeAction;
use Modules\Attribute\DTOs\AttributeDTO;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Repository\AttributeRepository;
use Tests\Unit\Actions\ActionTestCase;

class UpdateAttributeActionTest extends ActionTestCase
{
    use RefreshDatabase;

    public function test_execute_updates_attribute_with_dto(): void
    {
        // Arrange
        $attribute = Attribute::factory()->create([
            'name' => 'Old Color',
            'code' => 'old_color',
            'type' => Attribute::TYPE_STRING,
            'display' => Attribute::DISPLAY_INPUT,
            'is_required' => false,
            'is_filterable' => false,
            'is_configurable' => false,
        ]);

        $repository = new AttributeRepository();
        $action = new UpdateAttributeAction($repository);

        $dto = new AttributeDTO(
            id: $attribute->id,
            name: 'New Color',
            code: 'new_color',
            type: Attribute::TYPE_TEXT,
            display: Attribute::DISPLAY_SELECT,
            is_required: true,
            is_filterable: true,
            is_configurable: true,
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(Attribute::class, $result);
        $this->assertEquals('New Color', $result->name);
        $this->assertEquals('new_color', $result->code);
        $this->assertEquals(Attribute::TYPE_TEXT, $result->type);
        $this->assertEquals(Attribute::DISPLAY_SELECT, $result->display);
        $this->assertTrue((bool) $result->is_required);
        $this->assertTrue((bool) $result->is_filterable);
        $this->assertTrue((bool) $result->is_configurable);
        $this->assertDatabaseHas('attributes', [
            'id' => $attribute->id,
            'name' => 'New Color',
            'code' => 'new_color',
        ]);
    }

    public function test_execute_updates_partial_fields(): void
    {
        // Arrange
        $attribute = Attribute::factory()->create([
            'name' => 'Size',
            'code' => 'size',
            'type' => Attribute::TYPE_STRING,
            'is_required' => false,
        ]);

        $repository = new AttributeRepository();
        $action = new UpdateAttributeAction($repository);

        $dto = new AttributeDTO(
            id: $attribute->id,
            name: 'Product Size',
            code: 'size',
            type: Attribute::TYPE_STRING,
            display: Attribute::DISPLAY_INPUT,
            is_required: true,
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertEquals('Product Size', $result->name);
        $this->assertEquals('size', $result->code); // Unchanged
        $this->assertTrue((bool) $result->is_required);
    }

    public function test_execute_throws_exception_for_non_existent_attribute(): void
    {
        // Arrange
        $repository = new AttributeRepository();
        $action = new UpdateAttributeAction($repository);

        $dto = new AttributeDTO(
            id: 99999,
            name: 'Non Existent',
            code: 'non_existent',
            type: Attribute::TYPE_STRING,
            display: Attribute::DISPLAY_INPUT,
        );

        // Act & Assert
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $action->execute($dto);
    }

    public function test_execute_throws_exception_when_id_is_null(): void
    {
        // Arrange
        $repository = new AttributeRepository();
        $action = new UpdateAttributeAction($repository);

        $dto = new AttributeDTO(
            id: null,
            name: 'No ID',
            code: 'no_id',
            type: Attribute::TYPE_STRING,
            display: Attribute::DISPLAY_INPUT,
        );

        // Act & Assert
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $action->execute($dto);
    }
}
