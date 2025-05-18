<?php

declare(strict_types=1);

namespace Modules\Attribute\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Models\AttributeGroup;
use Modules\Attribute\Models\AttributeValue;
use Modules\Product\Models\Product;

class AttributeSystemSeeder extends Seeder
{
    public function run(): void
    {
        $group = AttributeGroup::factory()->create(['name' => 'General']);

        $color = Attribute::firstOrCreate(
            ['code' => 'color'],
            [
                'name' => 'Color',
                'type' => 'text',
                'display' => 'input',
                'is_filterable' => true,
                'is_configurable' => true,
                'attribute_group_id' => $group->id,
            ]
        );
        $size = Attribute::firstOrCreate(
            ['code' => 'size'],
            [
                'name' => 'Size',
                'type' => 'select',
                'display' => 'select',
                'is_filterable' => true,
                'is_configurable' => true,
                'attribute_group_id' => $group->id,
            ]
        );
        $isNew = Attribute::firstOrCreate(
            ['code' => 'is_new'],
            [
                'name' => 'Is New',
                'type' => 'boolean',
                'display' => 'checkbox',
                'is_filterable' => true,
                'is_configurable' => false,
                'attribute_group_id' => $group->id,
            ]
        );

        $product = Product::factory()->create();
        $product->attributes()->attach([$color->id, $size->id, $isNew->id]);

        AttributeValue::factory()->create([
            'product_id' => $product->id,
            'attribute_id' => $color->id,
            'text_value' => 'Red',
        ]);
        AttributeValue::factory()->create([
            'product_id' => $product->id,
            'attribute_id' => $size->id,
            'text_value' => 'Large',
        ]);
        AttributeValue::factory()->create([
            'product_id' => $product->id,
            'attribute_id' => $isNew->id,
            'boolean_value' => true,
        ]);
    }
}
