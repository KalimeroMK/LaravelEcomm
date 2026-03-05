<?php

declare(strict_types=1);

namespace Modules\Attribute\Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Models\AttributeGroup;
use Modules\Attribute\Models\AttributeOption;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AttributeTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_an_attribute(): void
    {
        $attribute = Attribute::create([
            'name' => 'Color',
            'code' => 'color',
            'type' => 'text',
            'display' => 'select',
            'is_required' => true,
            'is_filterable' => true,
            'is_configurable' => true,
        ]);

        $this->assertDatabaseHas('attributes', [
            'name' => 'Color',
            'code' => 'color',
        ]);
    }

    #[Test]
    public function it_has_valid_types(): void
    {
        $validTypes = [
            Attribute::TYPE_TEXT,
            Attribute::TYPE_BOOLEAN,
            Attribute::TYPE_DATE,
            Attribute::TYPE_INTEGER,
            Attribute::TYPE_FLOAT,
            Attribute::TYPE_STRING,
            Attribute::TYPE_URL,
            Attribute::TYPE_HEX,
            Attribute::TYPE_DECIMAL,
        ];

        foreach ($validTypes as $type) {
            $attribute = Attribute::factory()->create(['type' => $type]);
            $this->assertEquals($type, $attribute->type);
        }
    }

    #[Test]
    public function it_can_have_options(): void
    {
        $attribute = Attribute::factory()->create(['type' => 'select', 'display' => 'select']);

        AttributeOption::create([
            'attribute_id' => $attribute->id,
            'value' => 'red',
            'label' => 'Red',
            'sort_order' => 1,
        ]);

        $this->assertCount(1, $attribute->fresh()->options);
    }

    #[Test]
    public function it_returns_correct_value_column_name(): void
    {
        $testCases = [
            ['type' => 'text', 'expected' => 'text_value'],
            ['type' => 'boolean', 'expected' => 'boolean_value'],
            ['type' => 'date', 'expected' => 'date_value'],
            ['type' => 'integer', 'expected' => 'integer_value'],
            ['type' => 'float', 'expected' => 'float_value'],
            ['type' => 'string', 'expected' => 'string_value'],
            ['type' => 'url', 'expected' => 'url_value'],
            ['type' => 'hex', 'expected' => 'hex_value'],
            ['type' => 'decimal', 'expected' => 'decimal_value'],
        ];

        foreach ($testCases as $case) {
            $attribute = Attribute::factory()->create(['type' => $case['type']]);
            $this->assertEquals($case['expected'], $attribute->getValueColumnName());
        }
    }

    #[Test]
    public function it_can_belong_to_groups(): void
    {
        $attribute = Attribute::factory()->create();
        $group = AttributeGroup::factory()->create(['name' => 'General']);

        $attribute->groups()->attach($group->id);

        $this->assertCount(1, $attribute->fresh()->groups);
    }
}
