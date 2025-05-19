<?php

declare(strict_types=1);

namespace Modules\Attribute\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Models\AttributeGroup;

class AttributeSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            1 => AttributeGroup::firstOrCreate(['id' => 1], ['name' => 'General']),
            2 => AttributeGroup::firstOrCreate(['id' => 2], ['name' => 'Dimensions']),
            3 => AttributeGroup::firstOrCreate(['id' => 3], ['name' => 'Specifications']),
            4 => AttributeGroup::firstOrCreate(['id' => 4], ['name' => 'Marketing Info']),
        ];

        $attributes = [
            ['name' => 'Color', 'code' => 'color', 'type' => 'text', 'display' => 'input', 'attribute_group_id' => 1],
            ['name' => 'Size', 'code' => 'size', 'type' => 'select', 'display' => 'select', 'attribute_group_id' => 1],
            ['name' => 'Material', 'code' => 'material', 'type' => 'text', 'display' => 'input', 'attribute_group_id' => 3],
            ['name' => 'Brand', 'code' => 'brand', 'type' => 'text', 'display' => 'input', 'attribute_group_id' => 4],
            ['name' => 'Weight', 'code' => 'weight', 'type' => 'float', 'display' => 'input', 'attribute_group_id' => 3],
            ['name' => 'Length', 'code' => 'length', 'type' => 'float', 'display' => 'input', 'attribute_group_id' => 2],
            ['name' => 'Width', 'code' => 'width', 'type' => 'float', 'display' => 'input', 'attribute_group_id' => 2],
            ['name' => 'Height', 'code' => 'height', 'type' => 'float', 'display' => 'input', 'attribute_group_id' => 2],
            ['name' => 'Is New', 'code' => 'is_new', 'type' => 'boolean', 'display' => 'checkbox', 'attribute_group_id' => 4],
            ['name' => 'Release Date', 'code' => 'release_date', 'type' => 'date', 'display' => 'input', 'attribute_group_id' => 4],
        ];

        foreach ($attributes as $attr) {
            Attribute::firstOrCreate([
                'code' => $attr['code'],
            ], array_merge($attr, [
                'is_required' => false,
                'is_filterable' => true,
            ]));
        }
    }
}
