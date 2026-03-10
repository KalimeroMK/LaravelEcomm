<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Attribute;

use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Modules\Attribute\Actions\CreateAttributeAction;
use Modules\Attribute\DTOs\AttributeDTO;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Repository\AttributeRepository;
use Tests\Unit\Actions\ActionTestCase;

class CreateAttributeActionTest extends ActionTestCase
{
    use RefreshDatabase;

    public function test_execute_creates_attribute_with_dto(): void
    {
        // Arrange
        $repository = new AttributeRepository();
        $action = new CreateAttributeAction($repository);

        $dto = new AttributeDTO(
            id: null,
            name: 'Color',
            code: 'color',
            type: Attribute::TYPE_STRING,
            display: Attribute::DISPLAY_SELECT,
            is_required: true,
            is_filterable: true,
            is_configurable: true,
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(Attribute::class, $result);
        $this->assertEquals('Color', $result->name);
        $this->assertEquals('color', $result->code);
        $this->assertEquals(Attribute::TYPE_STRING, $result->type);
        $this->assertEquals(Attribute::DISPLAY_SELECT, $result->display);
        $this->assertTrue((bool) $result->is_required);
        $this->assertTrue((bool) $result->is_filterable);
        $this->assertTrue((bool) $result->is_configurable);
        $this->assertDatabaseHas('attributes', [
            'name' => 'Color',
            'code' => 'color',
            'type' => Attribute::TYPE_STRING,
        ]);
    }

    public function test_execute_creates_attribute_with_minimal_data(): void
    {
        // Arrange
        $repository = new AttributeRepository();
        $action = new CreateAttributeAction($repository);

        $dto = new AttributeDTO(
            id: null,
            name: 'Size',
            code: 'size',
            type: Attribute::TYPE_STRING,
            display: Attribute::DISPLAY_INPUT,
        );

        // Act
        $result = $action->execute($dto);

        // Assert
        $this->assertInstanceOf(Attribute::class, $result);
        $this->assertEquals('Size', $result->name);
        $this->assertFalse((bool) $result->is_required);
        $this->assertFalse((bool) $result->is_filterable);
        $this->assertFalse((bool) $result->is_configurable);
    }

    public function test_execute_creates_attribute_with_different_types(): void
    {
        // Arrange
        $repository = new AttributeRepository();
        $action = new CreateAttributeAction($repository);

        $types = [
            ['type' => Attribute::TYPE_TEXT, 'name' => 'Description'],
            ['type' => Attribute::TYPE_BOOLEAN, 'name' => 'Is Active'],
            ['type' => Attribute::TYPE_INTEGER, 'name' => 'Count'],
            ['type' => Attribute::TYPE_DATE, 'name' => 'Release Date'],
        ];

        foreach ($types as $typeData) {
            $dto = new AttributeDTO(
                id: null,
                name: $typeData['name'],
                code: strtolower(str_replace(' ', '_', $typeData['name'])),
                type: $typeData['type'],
                display: Attribute::DISPLAY_INPUT,
            );

            // Act
            $result = $action->execute($dto);

            // Assert
            $this->assertEquals($typeData['type'], $result->type);
        }

        $this->assertDatabaseCount('attributes', count($types));
    }
}
