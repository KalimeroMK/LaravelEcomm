<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Attribute;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Modules\Attribute\Actions\GetAllAttributesAction;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Repository\AttributeRepository;
use Tests\Unit\Actions\ActionTestCase;

class GetAllAttributesActionTest extends ActionTestCase
{
    use RefreshDatabase;

    public function test_execute_returns_empty_collection_when_no_attributes(): void
    {
        // Arrange
        $repository = new AttributeRepository();
        $action = new GetAllAttributesAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEmpty($result);
    }

    public function test_execute_returns_all_attributes(): void
    {
        // Arrange
        Attribute::factory()->create(['name' => 'Color', 'code' => 'color']);
        Attribute::factory()->create(['name' => 'Size', 'code' => 'size']);
        Attribute::factory()->create(['name' => 'Weight', 'code' => 'weight']);

        $repository = new AttributeRepository();
        $action = new GetAllAttributesAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(3, $result);
        $this->assertContainsOnlyInstancesOf(Attribute::class, $result);
    }

    public function test_execute_returns_attributes_with_options(): void
    {
        // Arrange
        $attribute = Attribute::factory()->create(['name' => 'Color', 'code' => 'color']);
        $attribute->options()->createMany([
            ['label' => 'Red', 'value' => '#FF0000'],
            ['label' => 'Blue', 'value' => '#0000FF'],
        ]);

        $repository = new AttributeRepository();
        $action = new GetAllAttributesAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertCount(1, $result);
        $firstAttribute = $result->first();
        $this->assertTrue($firstAttribute->relationLoaded('options'));
        $this->assertCount(2, $firstAttribute->options);
    }

    public function test_execute_returns_collection_with_correct_types(): void
    {
        // Arrange
        Attribute::factory()->create(['type' => Attribute::TYPE_STRING]);
        Attribute::factory()->create(['type' => Attribute::TYPE_INTEGER]);
        Attribute::factory()->create(['type' => Attribute::TYPE_BOOLEAN]);

        $repository = new AttributeRepository();
        $action = new GetAllAttributesAction($repository);

        // Act
        $result = $action->execute();

        // Assert
        $this->assertCount(3, $result);
        $types = $result->pluck('type')->toArray();
        $this->assertContains(Attribute::TYPE_STRING, $types);
        $this->assertContains(Attribute::TYPE_INTEGER, $types);
        $this->assertContains(Attribute::TYPE_BOOLEAN, $types);
    }
}
